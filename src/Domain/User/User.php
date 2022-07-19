<?php

declare(strict_types=1);

namespace App\Domain\User;

use DateTimeZone;
use DomainException;
use App\Domain\Shared\ValueObject\Locale;
use Doctrine\Common\Collections\Collection;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\User\Event\UserProfileChanged;
use App\Domain\User\Event\UserProjectsChanged;
use App\Domain\User\Event\UserTimezoneChanged;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\User\ValueObject\NotificationPreferences;
use App\Domain\User\Event\UserNotificationPreferencesChanged;
use SixtyEightPublishers\UserBundle\Domain\Event\UserCreated;
use SixtyEightPublishers\UserBundle\Domain\Command\CreateUserCommand;
use SixtyEightPublishers\UserBundle\Domain\Command\UpdateUserCommand;
use SixtyEightPublishers\UserBundle\Domain\Aggregate\User as BaseUser;
use SixtyEightPublishers\UserBundle\Domain\PasswordHashAlgorithmInterface;
use SixtyEightPublishers\UserBundle\Domain\CheckUsernameUniquenessInterface;
use SixtyEightPublishers\UserBundle\Domain\CheckEmailAddressUniquenessInterface;

final class User extends BaseUser
{
	private Locale $profileLocale;

	private DateTimeZone $timezone;

	/** @var \Doctrine\Common\Collections\Collection|\App\Domain\User\UserHasProject[] */
	private Collection $projects;

	private NotificationPreferences $notificationPreferences;

	/**
	 * {@inheritDoc}
	 */
	public static function create(CreateUserCommand $command, PasswordHashAlgorithmInterface $algorithm, CheckEmailAddressUniquenessInterface $checkEmailAddressUniqueness, CheckUsernameUniquenessInterface $checkUsernameUniqueness): self
	{
		$user = parent::create($command, $algorithm, $checkEmailAddressUniqueness, $checkUsernameUniqueness);

		if (!$command->hasParam('profile')) {
			throw new DomainException(sprintf(
				'Missing required parameter "profile" in the command %s.',
				CreateUserCommand::class
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

	/**
	 * @param \SixtyEightPublishers\UserBundle\Domain\Command\UpdateUserCommand            $command
	 * @param \SixtyEightPublishers\UserBundle\Domain\PasswordHashAlgorithmInterface       $algorithm
	 * @param \SixtyEightPublishers\UserBundle\Domain\CheckEmailAddressUniquenessInterface $checkEmailAddressUniqueness
	 * @param \SixtyEightPublishers\UserBundle\Domain\CheckUsernameUniquenessInterface     $checkUsernameUniqueness
	 *
	 * @return void
	 */
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

	/**
	 * @param \App\Domain\Shared\ValueObject\Locale $profileLocale
	 *
	 * @return void
	 */
	public function changeProfile(Locale $profileLocale): void
	{
		if (!$this->profileLocale->equals($profileLocale)) {
			$this->recordThat(UserProfileChanged::create($this->id, $profileLocale));
		}
	}

	/**
	 * @param \DateTimeZone $timezone
	 *
	 * @return void
	 */
	public function changeTimezone(DateTimeZone $timezone): void
	{
		if ($this->timezone->getName() !== $timezone->getName()) {
			$this->recordThat(UserTimezoneChanged::create($this->id, $timezone));
		}
	}

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId[] $projectIds
	 *
	 * @return void
	 */
	public function changeProjects(array $projectIds): void
	{
		if (!$this->areProjectsEqual($this->projects->map(static fn (UserHasProject $userHasProject): ProjectId => $userHasProject->projectId())->getValues(), $projectIds)) {
			$this->recordThat(UserProjectsChanged::create($this->id, $projectIds));
		}
	}

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId[] $projectIds
	 *
	 * @return void
	 */
	public function addProjects(array $projectIds): void
	{
		/** @var \App\Domain\Project\ValueObject\ProjectId[] $currentProjectIds */
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

	/**
	 * @param \App\Domain\User\ValueObject\NotificationPreferences $notificationPreferences
	 *
	 * @return void
	 */
	public function changeNotificationPreferences(NotificationPreferences $notificationPreferences): void
	{
		if (!$this->notificationPreferences->equals($notificationPreferences)) {
			$this->recordThat(UserNotificationPreferencesChanged::create($this->id, $notificationPreferences));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function whenUserCreated(UserCreated $event): void
	{
		parent::whenUserCreated($event);

		$this->timezone = new DateTimeZone('UTC');
		$this->projects = new ArrayCollection();
		$this->notificationPreferences = NotificationPreferences::empty();
	}

	/**
	 * @param \App\Domain\User\Event\UserProfileChanged $event
	 *
	 * @return void
	 */
	protected function whenUserProfileChanged(UserProfileChanged $event): void
	{
		$this->profileLocale = $event->profileLocale();
	}

	/**
	 * @param \App\Domain\User\Event\UserTimezoneChanged $event
	 *
	 * @return void
	 */
	protected function whenUserTimezoneChanged(UserTimezoneChanged $event): void
	{
		$this->timezone = $event->timezone();
	}

	/**
	 * @param \App\Domain\User\Event\UserProjectsChanged $event
	 *
	 * @return void
	 */
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

	/**
	 * @param \App\Domain\User\Event\UserNotificationPreferencesChanged $event
	 *
	 * @return void
	 */
	protected function whenUserNotificationPreferencesChanged(UserNotificationPreferencesChanged $event): void
	{
		$this->notificationPreferences = $event->notificationPreferences();
	}

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId[] $current
	 * @param \App\Domain\Project\ValueObject\ProjectId[] $new
	 *
	 * @return bool
	 */
	private function areProjectsEqual(array $current, array $new): bool
	{
		if (count($current) !== count($new)) {
			return FALSE;
		}

		foreach ($new as $projectId) {
			foreach ($current as $currentProjectId) {
				if ($currentProjectId->equals($projectId)) {
					continue 2;
				}
			}

			return FALSE;
		}

		return TRUE;
	}
}
