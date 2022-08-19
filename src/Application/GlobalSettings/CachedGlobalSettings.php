<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use Nette\Caching\Cache;
use Nette\Caching\Storage;
use App\Domain\GlobalSettings\ValueObject\ApiCache;

final class CachedGlobalSettings implements GlobalSettingsInterface
{
	private const CACHE_KEY = 'global_settings';

	private GlobalSettingsFactoryInterface $globalSettingsFactory;

	private Cache $cache;

	private ?GlobalSettingsInterface $inner = NULL;

	/**
	 * @param \App\Application\GlobalSettings\GlobalSettingsFactoryInterface $globalSettingsFactory
	 * @param \Nette\Caching\Storage                                         $storage
	 */
	public function __construct(GlobalSettingsFactoryInterface $globalSettingsFactory, Storage $storage)
	{
		$this->globalSettingsFactory = $globalSettingsFactory;
		$this->cache = new Cache($storage, self::class);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Throwable
	 */
	public function locales(): array
	{
		return $this->getInner()->locales();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Throwable
	 */
	public function defaultLocale(): Locale
	{
		return $this->getInner()->defaultLocale();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Throwable
	 */
	public function apiCache(): ApiCache
	{
		return $this->getInner()->apiCache();
	}

	/**
	 * {@inheritDoc}
	 */
	public function refresh(): void
	{
		if (NULL !== $this->inner) {
			$this->inner->refresh();
		}

		$this->cache->remove(self::CACHE_KEY);
		$this->inner = NULL;
	}

	/**
	 * @return \App\Application\GlobalSettings\GlobalSettingsInterface
	 * @throws \Throwable
	 */
	private function getInner(): GlobalSettingsInterface
	{
		return $this->inner ?? ($this->inner = $this->cache->load(self::CACHE_KEY, function (): GlobalSettingsInterface {
			return $this->globalSettingsFactory->create();
		}));
	}
}
