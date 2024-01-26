<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\GlobalSettings\ValueObject\ApiCache;
use App\Domain\GlobalSettings\ValueObject\AzureAuthSettings;
use App\Domain\GlobalSettings\ValueObject\CrawlerSettings;
use App\Domain\GlobalSettings\ValueObject\EnvironmentSettings;

interface GlobalSettingsInterface
{
    /**
     * @return array<Locale>
     */
    public function locales(): array;

    public function defaultLocale(): Locale;

    public function apiCache(): ApiCache;

    public function crawlerSettings(): CrawlerSettings;

    public function environmentSettings(): EnvironmentSettings;

    public function azureAuthSettings(): AzureAuthSettings;

    public function refresh(): void;
}
