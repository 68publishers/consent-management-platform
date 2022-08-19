<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\GlobalSettings\ValueObject\ApiCache;

final class GlobalSettings implements GlobalSettingsInterface
{
	private array $locales;

	private Locale $defaultLocale;

	private ApiCache $apiCache;

	/**
	 * @param array                                           $locales
	 * @param \App\Application\GlobalSettings\Locale          $defaultLocale
	 * @param \App\Domain\GlobalSettings\ValueObject\ApiCache $apiCache
	 */
	public function __construct(array $locales, Locale $defaultLocale, ApiCache $apiCache)
	{
		$this->locales = $locales;
		$this->defaultLocale = $defaultLocale;
		$this->apiCache = $apiCache;
	}

	/**
	 * @return static
	 */
	public static function default(): self
	{
		return new self([], Locale::unknown(), ApiCache::create([]));
	}

	/**
	 * {@inheritDoc}
	 */
	public function locales(): array
	{
		return $this->locales;
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaultLocale(): Locale
	{
		return $this->defaultLocale;
	}

	/**
	 * {@inheritDoc}
	 */
	public function apiCache(): ApiCache
	{
		return $this->apiCache;
	}

	/**
	 * {@inheritDoc}
	 */
	public function refresh(): void
	{
	}
}
