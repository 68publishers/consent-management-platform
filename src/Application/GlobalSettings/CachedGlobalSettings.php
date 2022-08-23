<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use Nette\Caching\Cache;
use Nette\Caching\Storage;
use App\Domain\GlobalSettings\ValueObject\ApiCache;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface;

final class CachedGlobalSettings implements GlobalSettingsInterface
{
	private const CACHE_KEY = 'global_settings';

	private GlobalSettingsFactoryInterface $globalSettingsFactory;

	private Cache $cache;

	private TranslatorLocalizerInterface $translatorLocalizer;

	private ?GlobalSettingsInterface $inner = NULL;

	/**
	 * @param \App\Application\GlobalSettings\GlobalSettingsFactoryInterface                    $globalSettingsFactory
	 * @param \Nette\Caching\Storage                                                            $storage
	 * @param \SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface $translatorLocalizer
	 */
	public function __construct(GlobalSettingsFactoryInterface $globalSettingsFactory, Storage $storage, TranslatorLocalizerInterface $translatorLocalizer)
	{
		$this->globalSettingsFactory = $globalSettingsFactory;
		$this->cache = new Cache($storage, self::class);
		$this->translatorLocalizer = $translatorLocalizer;
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

		$this->cache->clean([
			Cache::All => TRUE,
		]);
		$this->inner = NULL;
	}

	/**
	 * @return \App\Application\GlobalSettings\GlobalSettingsInterface
	 * @throws \Throwable
	 */
	private function getInner(): GlobalSettingsInterface
	{
		return $this->inner ?? ($this->inner = $this->cache->load($this->createKey(), function (): GlobalSettingsInterface {
			return $this->globalSettingsFactory->create();
		}));
	}

	/**
	 * @return string
	 */
	private function createKey(): string
	{
		return self::CACHE_KEY . '_' . $this->translatorLocalizer->getLocale();
	}
}
