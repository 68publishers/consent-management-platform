<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectActiveStateChanged extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private bool $active;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param bool                                      $active
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, bool $active): self
	{
		$event = self::occur($projectId->toString(), [
			'active' => $active,
		]);

		$event->projectId = $projectId;
		$event->active = $active;

		return $event;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}

	/**
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->active;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->active = (bool) $parameters['active'];
	}
}
