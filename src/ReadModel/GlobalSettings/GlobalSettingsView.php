<?php

declare(strict_types=1);

namespace App\ReadModel\GlobalSettings;

use App\Domain\GlobalSettings\ValueObject\ApiCache;
use App\Domain\GlobalSettings\ValueObject\CrawlerSettings;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use App\Domain\Shared\ValueObject\LocalesConfig;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class GlobalSettingsView extends AbstractView
{
    public GlobalSettingsId $id;

    public DateTimeImmutable $createdAt;

    public DateTimeImmutable $lastUpdateAt;

    public LocalesConfig $locales;

    public ApiCache $apiCache;

    public CrawlerSettings $crawlerSettings;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'lastUpdateAt' => $this->lastUpdateAt->format(DateTimeInterface::ATOM),
            'localesConfig' => [
                'locales' => $this->locales->locales()->toArray(),
                'defaultLocale' => $this->locales->defaultLocale()->value(),
            ],
            'apiCache' => [
                'cacheControlDirectives' => $this->apiCache->cacheControlDirectives(),
                'useEntityTag' => $this->apiCache->useEntityTag(),
            ],
            'crawlerSettings' => $this->crawlerSettings->values(),
        ];
    }
}
