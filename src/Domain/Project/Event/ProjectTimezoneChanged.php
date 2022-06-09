<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use DateTimeZone;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectTimezoneChanged extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private DateTimeZone $timezone;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \DateTimeZone                             $timezone
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, DateTimeZone $timezone): self
	{
		$event = self::occur($projectId->toString(), [
			'timezone' => $timezone->getName(),
		]);

		$event->projectId = $projectId;
		$event->timezone = $timezone;

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
	 * @return \DateTimeZone
	 */
	public function timezone(): DateTimeZone
	{
		return $this->timezone;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->timezone = new DateTimeZone($parameters['timezone']);
	}
}
