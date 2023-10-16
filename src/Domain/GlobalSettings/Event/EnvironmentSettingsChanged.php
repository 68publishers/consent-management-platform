<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\GlobalSettings\ValueObject\EnvironmentSettings;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class EnvironmentSettingsChanged extends AbstractDomainEvent
{
    private GlobalSettingsId $globalSettingsId;

    private EnvironmentSettings $environmentSettings;

    public static function create(GlobalSettingsId $globalSettingsId, EnvironmentSettings $environmentSettings): self
    {
        $event = self::occur($globalSettingsId->toString(), [
            'environment_settings' => $environmentSettings->toNative(),
        ]);

        $event->globalSettingsId = $globalSettingsId;
        $event->environmentSettings = $environmentSettings;

        return $event;
    }

    public function globalSettingsId(): GlobalSettingsId
    {
        return $this->globalSettingsId;
    }

    public function environmentSettings(): EnvironmentSettings
    {
        return $this->environmentSettings;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
        $this->environmentSettings = EnvironmentSettings::fromSafeNative($parameters['environment_settings']);
    }
}
