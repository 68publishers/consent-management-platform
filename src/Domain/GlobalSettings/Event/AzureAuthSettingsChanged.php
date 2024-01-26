<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\GlobalSettings\ValueObject\AzureAuthSettings;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class AzureAuthSettingsChanged extends AbstractDomainEvent
{
    private GlobalSettingsId $globalSettingsId;

    private AzureAuthSettings $azureAuthSettings;

    public static function create(GlobalSettingsId $globalSettingsId, AzureAuthSettings $azureAuthSettings): self
    {
        $event = self::occur($globalSettingsId->toString(), [
            'azure_auth_settings' => $azureAuthSettings->values(),
        ]);

        $event->globalSettingsId = $globalSettingsId;
        $event->azureAuthSettings = $azureAuthSettings;

        return $event;
    }

    public function globalSettingsId(): GlobalSettingsId
    {
        return $this->globalSettingsId;
    }

    public function azureAuthSettings(): AzureAuthSettings
    {
        return $this->azureAuthSettings;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
        $this->azureAuthSettings = AzureAuthSettings::fromArray($parameters['azure_auth_settings']);
    }
}
