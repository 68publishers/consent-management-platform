<?php

declare(strict_types=1);

namespace App\Subscribers\GlobalSettings;

use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\GlobalSettings\Event\ApiCacheSettingsChanged;
use App\Domain\GlobalSettings\Event\CrawlerSettingsChanged;
use App\Domain\GlobalSettings\Event\GlobalSettingsCreated;
use App\Domain\GlobalSettings\Event\LocalizationSettingsChanged;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

final class RefreshGlobalSettingsWhenChanged implements EventHandlerInterface, MessageSubscriberInterface
{
    public function __construct(
        private readonly GlobalSettingsInterface $globalSettings,
    ) {}

    public static function getHandledMessages(): iterable
    {
        yield GlobalSettingsCreated::class;
        yield LocalizationSettingsChanged::class;
        yield ApiCacheSettingsChanged::class;
        yield CrawlerSettingsChanged::class;
    }

    public function __invoke(): void
    {
        $this->globalSettings->refresh();
    }
}
