<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class UserProjectsChanged extends AbstractDomainEvent
{
	private UserId $userId;

	/** @var \App\Domain\Project\ValueObject\ProjectId[] */
	private array $projectIds;

	/**
	 * @param \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId $userId
	 * @param array                                                      $projectIds
	 *
	 * @return static
	 */
	public static function create(UserId $userId, array $projectIds): self
	{
		$event = self::occur($userId->toString(), [
			'project_ids' => array_map(static fn (ProjectId $projectId): string => $projectId->toString(), $projectIds),
		]);

		$event->userId = $userId;
		$event->projectIds = $projectIds;

		return $event;
	}

	/**
	 * @return \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId
	 */
	public function userId(): UserId
	{
		return $this->userId;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId[]
	 */
	public function projectIds(): array
	{
		return $this->projectIds;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->userId = UserId::fromUuid($this->aggregateId()->id());
		$this->projectIds = array_map(static fn (string $projectId): ProjectId => ProjectId::fromString($projectId), $parameters['project_ids']);
	}
}
