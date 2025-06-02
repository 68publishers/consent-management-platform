<?php

declare(strict_types=1);

namespace App\Subscribers\GlobalSettings;

use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\GlobalSettings\Event\ApiCacheSettingsChanged;
use App\Domain\GlobalSettings\Event\AzureAuthSettingsChanged;
use App\Domain\GlobalSettings\Event\CrawlerSettingsChanged;
use App\Domain\GlobalSettings\Event\EnvironmentSettingsChanged;
use App\Domain\GlobalSettings\Event\GlobalSettingsCreated;
use App\Domain\GlobalSettings\Event\LocalizationSettingsChanged;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final readonly class RefreshGlobalSettingsWhenChanged implements EventHandlerInterface
{
    public function __construct(
        private GlobalSettingsInterface $globalSettings,
    ) {}

    #[AsMessageHandler(bus: 'event', handles: GlobalSettingsCreated::class)]
    #[AsMessageHandler(bus: 'event', handles: LocalizationSettingsChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: ApiCacheSettingsChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CrawlerSettingsChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: EnvironmentSettingsChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: AzureAuthSettingsChanged::class)]
    public function __invoke(): void
    {
        $this->globalSettings->refresh();
    }
}
