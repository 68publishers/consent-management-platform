<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\Shared\ValueObject\Locales;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class GlobalSettingsUpdated extends AbstractDomainEvent
{
	private GlobalSettingsId $globalSettingsId;

	private Locales $locales;

	/**
	 * @param \App\Domain\GlobalSettings\ValueObject\GlobalSettingsId $globalSettingsId
	 * @param \App\Domain\Shared\ValueObject\Locales                  $locales
	 *
	 * @return static
	 */
	public static function create(GlobalSettingsId $globalSettingsId, Locales $locales): self
	{
		$event = self::occur($globalSettingsId->toString(), [
			'locales' => $locales->toArray(),
		]);

		$event->globalSettingsId = $globalSettingsId;
		$event->locales = $locales;

		return $event;
	}

	/**
	 * @return \App\Domain\GlobalSettings\ValueObject\GlobalSettingsId
	 */
	public function getGlobalSettingsId(): GlobalSettingsId
	{
		return $this->globalSettingsId;
	}

	/**
	 * @return \App\Domain\Shared\ValueObject\Locales
	 */
	public function getLocales(): Locales
	{
		return $this->locales;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
		$this->locales = Locales::reconstitute($parameters['locales']);
	}
}
