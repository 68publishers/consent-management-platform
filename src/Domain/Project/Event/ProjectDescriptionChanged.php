<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Description;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectDescriptionChanged extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private Description $description;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId   $projectId
	 * @param \App\Domain\Project\ValueObject\Description $description
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, Description $description): self
	{
		$event = self::occur($projectId->toString(), [
			'description' => $description->value(),
		]);

		$event->projectId = $projectId;
		$event->description = $description;

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
	 * @return \App\Domain\Project\ValueObject\Description
	 */
	public function description(): Description
	{
		return $this->description;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->description = Description::fromValue($parameters['description']);
	}
}
