<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Shared\ValueObject\LocalesConfig;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class LocalizationSettingsChanged extends AbstractDomainEvent
{
	private GlobalSettingsId $globalSettingsId;

	private LocalesConfig $locales;

	/**
	 * @param \App\Domain\GlobalSettings\ValueObject\GlobalSettingsId $globalSettingsId
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig            $locales
	 *
	 * @return static
	 */
	public static function create(GlobalSettingsId $globalSettingsId, LocalesConfig $locales): self
	{
		$event = self::occur($globalSettingsId->toString(), [
			'locales' => $locales->locales()->toArray(),
			'default_locale' => $locales->defaultLocale()->value(),
		]);

		$event->globalSettingsId = $globalSettingsId;
		$event->locales = $locales;

		return $event;
	}

	/**
	 * @return \App\Domain\GlobalSettings\ValueObject\GlobalSettingsId
	 */
	public function globalSettingsId(): GlobalSettingsId
	{
		return $this->globalSettingsId;
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
		$this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
		$this->locales = LocalesConfig::create(Locales::reconstitute($parameters['locales']), Locale::fromValue($parameters['default_locale']));
	}
}
