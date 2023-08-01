<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\GlobalSettings\ValueObject\CrawlerSettings;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CrawlerSettingsChanged extends AbstractDomainEvent
{
    private GlobalSettingsId $globalSettingsId;

    private CrawlerSettings $crawlerSettings;

    public static function create(GlobalSettingsId $globalSettingsId, CrawlerSettings $crawlerSettings): self
    {
        $event = self::occur($globalSettingsId->toString(), [
            'crawler_settings' => $crawlerSettings->values(),
        ]);

        $event->globalSettingsId = $globalSettingsId;
        $event->crawlerSettings = $crawlerSettings;

        return $event;
    }

    public function globalSettingsId(): GlobalSettingsId
    {
        return $this->globalSettingsId;
    }

    public function crawlerSettings(): CrawlerSettings
    {
        return $this->crawlerSettings;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
        $this->crawlerSettings = CrawlerSettings::fromArray($parameters['crawler_settings']);
    }
}
