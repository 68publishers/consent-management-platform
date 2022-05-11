<?php

declare(strict_types=1);

namespace App\Domain\User;

use Doctrine\Common\Collections\Collection;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\User\Event\UserProjectsChanged;
use Doctrine\Common\Collections\ArrayCollection;
use SixtyEightPublishers\UserBundle\Domain\Command\CreateUserCommand;
use SixtyEightPublishers\UserBundle\Domain\Command\UpdateUserCommand;
use SixtyEightPublishers\UserBundle\Domain\Aggregate\User as BaseUser;
use SixtyEightPublishers\UserBundle\Domain\PasswordHashAlgorithmInterface;
use SixtyEightPublishers\UserBundle\Domain\CheckUsernameUniquenessInterface;
use SixtyEightPublishers\UserBundle\Domain\CheckEmailAddressUniquenessInterface;

final class User extends BaseUser
{
	/** @var \Doctrine\Common\Collections\Collection|\App\Domain\User\UserHasProject[] */
	private Collection $projects;

	/**
	 * {@inheritDoc}
	 */
	public static function create(CreateUserCommand $command, PasswordHashAlgorithmInterface $algorithm, CheckEmailAddressUniquenessInterface $checkEmailAddressUniqueness, CheckUsernameUniquenessInterface $checkUsernameUniqueness): self
	{
		$user = parent::create($command, $algorithm, $checkEmailAddressUniqueness, $checkUsernameUniqueness);
		$user->projects = new ArrayCollection();

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

		if ($command->hasParam('project_ids')) {
			$this->changeProjects(array_map(static fn (string $projectId): ProjectId => ProjectId::fromString($projectId), $command->getParam('project_ids')));
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
