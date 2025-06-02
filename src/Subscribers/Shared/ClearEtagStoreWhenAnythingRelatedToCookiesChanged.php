<?php

declare(strict_types=1);

namespace App\Subscribers\Shared;

use App\Api\Cache\EtagStoreInterface;
use App\Domain\Category\Category;
use App\Domain\Category\Event\CategoryActiveStateChanged;
use App\Domain\Category\Event\CategoryCodeChanged;
use App\Domain\Category\Event\CategoryNameUpdated;
use App\Domain\Cookie\Cookie;
use App\Domain\Cookie\Event\CookieActiveStateChanged;
use App\Domain\Cookie\Event\CookieCategoryChanged;
use App\Domain\Cookie\Event\CookieCreated;
use App\Domain\Cookie\Event\CookieEnvironmentsChanged;
use App\Domain\Cookie\Event\CookieNameChanged;
use App\Domain\Cookie\Event\CookieProcessingTimeChanged;
use App\Domain\Cookie\Event\CookiePurposeChanged;
use App\Domain\CookieProvider\CookieProvider;
use App\Domain\CookieProvider\Event\CookieProviderActiveStateChanged;
use App\Domain\CookieProvider\Event\CookieProviderCodeChanged;
use App\Domain\CookieProvider\Event\CookieProviderLinkChanged;
use App\Domain\CookieProvider\Event\CookieProviderNameChanged;
use App\Domain\CookieProvider\Event\CookieProviderPurposeChanged;
use App\Domain\CookieProvider\Event\CookieProviderTypeChanged;
use App\Domain\GlobalSettings\Event\ApiCacheSettingsChanged;
use App\Domain\GlobalSettings\Event\EnvironmentSettingsChanged;
use App\Domain\Project\Event\ProjectActiveStateChanged;
use App\Domain\Project\Event\ProjectCodeChanged;
use App\Domain\Project\Event\ProjectCookieProviderAdded;
use App\Domain\Project\Event\ProjectCookieProviderRemoved;
use App\Domain\Project\Event\ProjectEnvironmentsChanged;
use App\Domain\Project\Event\ProjectLocalesChanged;
use App\Domain\Project\Event\ProjectTemplateChanged;
use App\Domain\Project\Project;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AggregateDeleted;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final readonly class ClearEtagStoreWhenAnythingRelatedToCookiesChanged implements EventHandlerInterface
{
    public function __construct(
        private EtagStoreInterface $etagStore,
    ) {}

    # cookie creation & updates
    #[AsMessageHandler(bus: 'event', handles: CookieCreated::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieCategoryChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieActiveStateChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieNameChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieProcessingTimeChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookiePurposeChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieEnvironmentsChanged::class)]
    # cookie creation & updates
    #[AsMessageHandler(bus: 'event', handles: CookieProviderActiveStateChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieProviderCodeChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieProviderLinkChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieProviderNameChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieProviderPurposeChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CookieProviderTypeChanged::class)]
    # category updates
    #[AsMessageHandler(bus: 'event', handles: CategoryActiveStateChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CategoryCodeChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: CategoryNameUpdated::class)]
    # project changes
    #[AsMessageHandler(bus: 'event', handles: ProjectActiveStateChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: ProjectCodeChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: ProjectCookieProviderAdded::class)]
    #[AsMessageHandler(bus: 'event', handles: ProjectCookieProviderRemoved::class)]
    #[AsMessageHandler(bus: 'event', handles: ProjectLocalesChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: ProjectTemplateChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: ProjectEnvironmentsChanged::class)]
    # global settings changes
    #[AsMessageHandler(bus: 'event', handles: ApiCacheSettingsChanged::class)]
    #[AsMessageHandler(bus: 'event', handles: EnvironmentSettingsChanged::class)]
    public function __invoke(): void
    {
        $this->etagStore->clear();
    }

    #[AsMessageHandler(bus: 'event')]
    public function whenAggregateDeleted(AggregateDeleted $event): void
    {
        $classnames = [
            Cookie::class,
            CookieProvider::class,
            Category::class,
            Project::class,
        ];

        foreach ($classnames as $classname) {
            if (is_a($classname, $event->aggregateClassname(), true)) {
                $this->etagStore->clear();

                break;
            }
        }
    }
}
