<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\GlobalSettings\ValueObject\ApiCache;
use App\Domain\GlobalSettings\ValueObject\CrawlerSettings;
use App\Domain\GlobalSettings\ValueObject\EnvironmentSettings;

final class GlobalSettings implements GlobalSettingsInterface
{
    /**
     * @param array<Locale> $locales
     */
    public function __construct(
        private readonly array $locales,
        private readonly Locale $defaultLocale,
        private readonly ApiCache $apiCache,
        private readonly CrawlerSettings $crawlerSettings,
        private readonly EnvironmentSettings $environmentSettings,
    ) {}

    public static function default(): self
    {
        return new self(
            [],
            Locale::unknown(),
            ApiCache::create(),
            CrawlerSettings::fromValues(false, null, null, null, null),
            EnvironmentSettings::createDefault(),
        );
    }

    public function locales(): array
    {
        return $this->locales;
    }

    public function defaultLocale(): Locale
    {
        return $this->defaultLocale;
    }

    public function apiCache(): ApiCache
    {
        return $this->apiCache;
    }

    public function crawlerSettings(): CrawlerSettings
    {
        return $this->crawlerSettings;
    }

    public function environmentSettings(): EnvironmentSettings
    {
        return $this->environmentSettings;
    }

    public function refresh(): void
    {
    }
}
