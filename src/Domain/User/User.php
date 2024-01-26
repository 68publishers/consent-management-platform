<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\User\Command\StoreExternalAuthenticationCommand;
use App\Domain\User\Event\UserExternallyAuthenticated;
use App\Domain\User\Event\UserNotificationPreferencesChanged;
use App\Domain\User\Event\UserProfileChanged;
use App\Domain\User\Event\UserProjectsChanged;
use App\Domain\User\Event\UserTimezoneChanged;
use App\Domain\User\ValueObject\AuthProviderCode;
use App\Domain\User\ValueObject\AuthResourceOwnerId;
use App\Domain\User\ValueObject\AuthToken;
use App\Domain\User\ValueObject\NotificationPreferences;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainException;
use SixtyEightPublishers\UserBundle\Domain\Aggregate\User as BaseUser;
use SixtyEightPublishers\UserBundle\Domain\CheckEmailAddressUniquenessInterface;
use SixtyEightPublishers\UserBundle\Domain\CheckUsernameUniquenessInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\CreateUserCommand;
use SixtyEightPublishers\UserBundle\Domain\Command\UpdateUserCommand;
use SixtyEightPublishers\UserBundle\Domain\Event\UserCreated;
use SixtyEightPublishers\UserBundle\Domain\Event\UserRolesChanged;
use SixtyEightPublishers\UserBundle\Domain\PasswordHashAlgorithmInterface;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\Roles;

final class User extends BaseUser
{
    private Locale $profileLocale;

    private DateTimeZone $timezone;

    /** @var Collection<UserHasProject> */
    private Collection $projects;

    /** @var Collection<string, ExternalAuth> */
    private Collection $externalAuths;

    private NotificationPreferences $notificationPreferences;

    public static function create(CreateUserCommand $command, PasswordHashAlgorithmInterface $algorithm, CheckEmailAddressUniquenessInterface $checkEmailAddressUniqueness, CheckUsernameUniquenessInterface $checkUsernameUniqueness): self
    {
        $user = parent::create($command, $algorithm, $checkEmailAddressUniqueness, $checkUsernameUniqueness);

        if (!$command->hasParam('profile')) {
            throw new DomainException(sprintf(
                'Missing required parameter "profile" in the command %s.',
                CreateUserCommand::class,
            ));
        }

        $user->recordThat(UserProfileChanged::create($user->id, Locale::fromValue($command->getParam('profile'))));

        if ($command->hasParam('timezone')) {
            $user->changeTimezone(new DateTimeZone($command->getParam('timezone')));
        }

        if ($command->hasParam('project_ids') && !empty($command->getParam('project_ids'))) {
            $projectIds = array_map(static fn (string $projectId): ProjectId => ProjectId::fromString($projectId), $command->getParam('project_ids'));

            $user->changeProjects($projectIds);
        }

        return $user;
    }

    public function update(UpdateUserCommand $command, PasswordHashAlgorithmInterface $algorithm, CheckEmailAddressUniquenessInterface $checkEmailAddressUniqueness, CheckUsernameUniquenessInterface $checkUsernameUniqueness): void
    {
        parent::update($command, $algorithm, $checkEmailAddressUniqueness, $checkUsernameUniqueness);

        if ($command->hasParam('profile')) {
            $this->changeProfile(Locale::fromValue($command->getParam('profile')));
        }

        if ($command->hasParam('timezone')) {
            $this->changeTimezone(new DateTimeZone($command->getParam('timezone')));
        }

        if ($command->hasParam('project_ids')) {
            $this->changeProjects(array_map(static fn (string $projectId): ProjectId => ProjectId::fromString($projectId), $command->getParam('project_ids')));
        }
    }

    public function storeExternalAuthentication(StoreExternalAuthenticationCommand $command): void
    {
        $providerCode = AuthProviderCode::fromValue($command->providerCode());
        $resourceOwnerId = AuthResourceOwnerId::fromValue($command->resourceOwnerId());
        $token = AuthToken::fromValue($command->token());
        $refreshToken = AuthToken::fromValue($command->refreshToken());
        $roles = Roles::reconstitute($command->roles());

        $externalAuth = $this->externalAuths->get($providerCode->value());

        if (null === $externalAuth
            || !$externalAuth->getResourceOwnerId()->equals($resourceOwnerId)
            || !$externalAuth->getToken()->equals($token)
            || !$externalAuth->getRefreshToken()->equals($refreshToken)
        ) {
            $this->recordThat(UserExternallyAuthenticated::create(
                userId: $this->id,
                providerCode: $providerCode,
                resourceOwnerId: $resourceOwnerId,
                token: $token,
                refreshToken: $refreshToken,
            ));
        }

        if (!$this->roles->equals($roles)) {
            $this->recordThat(UserRolesChanged::create(
                userId: $this->id,
                roles: $roles,
            ));
        }
    }

    public function changeProfile(Locale $profileLocale): void
    {
        if (!$this->profileLocale->equals($profileLocale)) {
            $this->recordThat(UserProfileChanged::create($this->id, $profileLocale));
        }
    }

    public function changeTimezone(DateTimeZone $timezone): void
    {
        if ($this->timezone->getName() !== $timezone->getName()) {
            $this->recordThat(UserTimezoneChanged::create($this->id, $timezone));
        }
    }

    /**
     * @param array<ProjectId> $projectIds
     */
    public function changeProjects(array $projectIds): void
    {
        if (!$this->areProjectsEqual($this->projects->map(static fn (UserHasProject $userHasProject): ProjectId => $userHasProject->projectId())->getValues(), $projectIds)) {
            $this->recordThat(UserProjectsChanged::create($this->id, $projectIds));
        }
    }

    /**
     * @param array<ProjectId> $projectIds
     */
    public function addProjects(array $projectIds): void
    {
        /** @var array<ProjectId> $currentProjectIds */
        $currentProjectIds = $this->projects->map(static fn (UserHasProject $userHasProject): ProjectId => $userHasProject->projectId())->getValues();
        $newProjectIds = $currentProjectIds;

        foreach ($projectIds as $projectId) {
            foreach ($newProjectIds as $pId) {
                if ($pId->equals($projectId)) {
                    continue 2;
                }
            }

            $newProjectIds[] = $projectId;
        }

        if (!$this->areProjectsEqual($currentProjectIds, $newProjectIds)) {
            $this->recordThat(UserProjectsChanged::create($this->id, $newProjectIds));
        }
    }

    public function changeNotificationPreferences(NotificationPreferences $notificationPreferences): void
    {
        if (!$this->notificationPreferences->equals($notificationPreferences)) {
            $this->recordThat(UserNotificationPreferencesChanged::create($this->id, $notificationPreferences));
        }
    }

    protected function whenUserCreated(UserCreated $event): void
    {
        parent::whenUserCreated($event);

        $this->timezone = new DateTimeZone('UTC');
        $this->projects = new ArrayCollection();
        $this->externalAuths = new ArrayCollection();
        $this->notificationPreferences = NotificationPreferences::empty();
    }

    protected function whenUserProfileChanged(UserProfileChanged $event): void
    {
        $this->profileLocale = $event->profileLocale();
    }

    protected function whenUserTimezoneChanged(UserTimezoneChanged $event): void
    {
        $this->timezone = $event->timezone();
    }

    protected function whenUserProjectsChanged(UserProjectsChanged $event): void
    {
        $newProjectIds = $event->projectIds();

        foreach ($this->projects as $userHasProject) {
            foreach ($newProjectIds as $i => $projectId) {
                if ($projectId->equals($userHasProject->projectId())) {
                    unset($newProjectIds[$i]);

                    continue 2;
                }
            }

            $this->projects->removeElement($userHasProject);
        }

        foreach ($newProjectIds as $newProjectId) {
            $this->projects->add(UserHasProject::create($this, $newProjectId));
        }
    }

    protected function whenUserNotificationPreferencesChanged(UserNotificationPreferencesChanged $event): void
    {
        $this->notificationPreferences = $event->notificationPreferences();
    }

    protected function whenUserExternallyAuthenticated(UserExternallyAuthenticated $event): void
    {
        $externalAuth = $this->externalAuths->get($event->providerCode()->value());

        if (null !== $externalAuth) {
            $externalAuth->updateTokens(
                resourceOwnerId: $event->resourceOwnerId(),
                token: $event->token(),
                refreshToken: $event->refreshToken(),
            );

            return;
        }

        $this->externalAuths->set(
            key: $event->providerCode()->value(),
            value: new ExternalAuth(
                user: $this,
                providerCode: $event->providerCode(),
                createdAt: $event->createdAt(),
                resourceOwnerId: $event->resourceOwnerId(),
                token: $event->token(),
                refreshToken: $event->refreshToken(),
            ),
        );
    }

    /**
     * @param array<ProjectId> $current
     * @param array<ProjectId> $new
     */
    private function areProjectsEqual(array $current, array $new): bool
    {
        if (count($current) !== count($new)) {
            return false;
        }

        foreach ($new as $projectId) {
            foreach ($current as $currentProjectId) {
                if ($currentProjectId->equals($projectId)) {
                    continue 2;
                }
            }

            return false;
        }

        return true;
    }
}
