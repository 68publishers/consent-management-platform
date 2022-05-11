<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\Color;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectColorChanged extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private Color $color;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \App\Domain\Project\ValueObject\Color     $color
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, Color $color): self
	{
		$event = self::occur($projectId->toString(), [
			'color' => $color->value(),
		]);

		$event->projectId = $projectId;
		$event->color = $color;

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
	 * @return \App\Domain\Project\ValueObject\Color
	 */
	public function color(): Color
	{
		return $this->color;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->color = Color::fromValue($parameters['color']);
	}
}
