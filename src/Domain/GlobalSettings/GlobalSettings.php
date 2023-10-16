<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings;

use App\Domain\GlobalSettings\Event\ApiCacheSettingsChanged;
use App\Domain\GlobalSettings\Event\CrawlerSettingsChanged;
use App\Domain\GlobalSettings\Event\EnvironmentSettingsChanged;
use App\Domain\GlobalSettings\Event\GlobalSettingsCreated;
use App\Domain\GlobalSettings\Event\LocalizationSettingsChanged;
use App\Domain\GlobalSettings\ValueObject\ApiCache;
use App\Domain\GlobalSettings\ValueObject\CrawlerSettings;
use App\Domain\GlobalSettings\ValueObject\EnvironmentSettings;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Shared\ValueObject\LocalesConfig;
use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;

final class GlobalSettings implements AggregateRootInterface
{
    use AggregateRootTrait;

    private GlobalSettingsId $id;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $lastUpdateAt;

    private LocalesConfig $locales;

    private ApiCache $apiCache;

    private CrawlerSettings $crawlerSettings;

    private EnvironmentSettings $environmentSettings;

    public static function createEmpty(): self
    {
        $globalSettings = new self();

        $globalSettings->recordThat(GlobalSettingsCreated::create(GlobalSettingsId::new()));

        return $globalSettings;
    }

    public function updateLocalizationSettings(LocalesConfig $localesConfig): void
    {
        if (!$this->locales->equals($localesConfig)) {
            $this->recordThat(LocalizationSettingsChanged::create($this->id, $localesConfig));
        }
    }

    public function updateApiCacheSettings(ApiCache $apiCache): void
    {
        if (!$this->apiCache->equals($apiCache)) {
            $this->recordThat(ApiCacheSettingsChanged::create($this->id, $apiCache));
        }
    }

    public function updateCrawlerSettings(CrawlerSettings $crawlerSettings): void
    {
        if (!$this->crawlerSettings->equals($crawlerSettings)) {
            $this->recordThat(CrawlerSettingsChanged::create($this->id, $crawlerSettings));
        }
    }

    public function updateEnvironmentSettings(EnvironmentSettings $environmentSettings): void
    {
        if (!$this->environmentSettings->equals($environmentSettings)) {
            $this->recordThat(EnvironmentSettingsChanged::create($this->id, $environmentSettings));
        }
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromUuid($this->id->id());
    }

    protected function whenGlobalSettingsCreated(GlobalSettingsCreated $event): void
    {
        $this->id = $event->globalSettingsId();
        $this->createdAt = $event->createdAt();
        $this->lastUpdateAt = $event->createdAt();
        $this->locales = LocalesConfig::create(Locales::reconstitute(['en']), Locale::fromValue('en')); // setup defaults to en
        $this->apiCache = ApiCache::create();
        $this->crawlerSettings = CrawlerSettings::fromValues(false, null, null, null, null);
        $this->environmentSettings = EnvironmentSettings::createDefault();
    }

    protected function whenLocalizationSettingsChanged(LocalizationSettingsChanged $event): void
    {
        $this->lastUpdateAt = $event->createdAt();
        $this->locales = $event->locales();
    }

    protected function whenApiCacheSettingsChanged(ApiCacheSettingsChanged $event): void
    {
        $this->lastUpdateAt = $event->createdAt();
        $this->apiCache = $event->apiCache();
    }

    protected function whenCrawlerSettingsChanged(CrawlerSettingsChanged $event): void
    {
        $this->lastUpdateAt = $event->createdAt();
        $this->crawlerSettings = $event->crawlerSettings();
    }

    protected function whenEnvironmentSettingsChanged(EnvironmentSettingsChanged $event): void
    {
        $this->lastUpdateAt = $event->createdAt();
        $this->environmentSettings = $event->environmentSettings();
    }
}
