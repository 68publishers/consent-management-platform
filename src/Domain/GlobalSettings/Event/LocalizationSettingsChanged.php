<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Shared\ValueObject\LocalesConfig;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class LocalizationSettingsChanged extends AbstractDomainEvent
{
    private GlobalSettingsId $globalSettingsId;

    private LocalesConfig $locales;

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

    public function globalSettingsId(): GlobalSettingsId
    {
        return $this->globalSettingsId;
    }

    public function locales(): LocalesConfig
    {
        return $this->locales;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
        $this->locales = LocalesConfig::create(Locales::reconstitute($parameters['locales']), Locale::fromValue($parameters['default_locale']));
    }
}
