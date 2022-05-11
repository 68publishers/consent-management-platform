<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\Name;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectNameChanged extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private Name $name;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \App\Domain\Project\ValueObject\Name      $name
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, Name $name): self
	{
		$event = self::occur($projectId->toString(), [
			'name' => $name->value(),
		]);

		$event->projectId = $projectId;
		$event->name = $name;

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
	 * @return \App\Domain\Project\ValueObject\Name
	 */
	public function name(): Name
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->name = Name::fromValue($parameters['name']);
	}
}
