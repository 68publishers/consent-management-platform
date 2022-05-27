<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\LocalesConfig;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectLocalesChanged extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private LocalesConfig $locales;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId    $projectId
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig $locales
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, LocalesConfig $locales): self
	{
		$event = self::occur($projectId->toString(), [
			'locales' => $locales->locales()->toArray(),
			'default_locale' => $locales->defaultLocale()->value(),
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
	 * @return \App\Domain\Shared\ValueObject\LocalesConfig
	 */
	public function locales(): LocalesConfig
	{
		return $this->locales;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->locales = LocalesConfig::create(Locales::reconstitute($parameters['locales']), Locale::fromValue($parameters['default_locale']));
	}
}
