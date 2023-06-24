<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\GlobalSettings\ValueObject\ApiCache;
use App\Domain\GlobalSettings\ValueObject\CrawlerSettings;

final class GlobalSettings implements GlobalSettingsInterface
{
	private array $locales;

	private Locale $defaultLocale;

	private ApiCache $apiCache;

	private CrawlerSettings $crawlerSettings;

	/**
	 * @param array<Locale> $locales
	 */
	public function __construct(array $locales, Locale $defaultLocale, ApiCache $apiCache, CrawlerSettings $crawlerSettings)
	{
		$this->locales = $locales;
		$this->defaultLocale = $defaultLocale;
		$this->apiCache = $apiCache;
		$this->crawlerSettings = $crawlerSettings;
	}

	public static function default(): self
	{
		return new self(
			[],
			Locale::unknown(),
			ApiCache::create([]),
			CrawlerSettings::fromValues(NULL, NULL, NULL, NULL)
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

	public function refresh(): void
	{
	}
}
