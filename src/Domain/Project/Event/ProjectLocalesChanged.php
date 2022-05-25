<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectLocalesChanged extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private Locales $locales;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \App\Domain\Shared\ValueObject\Locales    $locales
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, Locales $locales): self
	{
		$event = self::occur($projectId->toString(), [
			'locales' => $locales->toArray(),
		]);

		$event->projectId = $projectId;
		$event->locales = $locales;

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
	 * @return \App\Domain\Shared\ValueObject\Locales
	 */
	public function locales(): Locales
	{
		return $this->locales;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->locales = Locales::reconstitute($parameters['locales']);
	}
}
