<?php

declare(strict_types=1);

namespace App\Application\Localization;

use RuntimeException;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface;

final class Locales
{
	private string $vendorDir;

	private TranslatorLocalizerInterface $translatorLocalizer;

	private array $cache = [];

	/**
	 * @param string                                                                            $vendorDir
	 * @param \SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface $translatorLocalizer
	 */
	public function __construct(string $vendorDir, TranslatorLocalizerInterface $translatorLocalizer)
	{
		$this->vendorDir = $vendorDir;
		$this->translatorLocalizer = $translatorLocalizer;
	}

	/**
	 * @param string|NULL $locale
	 *
	 * @return array
	 */
	public function get(?string $locale = NULL): array
	{
		$locale = $locale ?? $this->translatorLocalizer->getLocale();

		if (isset($this->cache[$locale])) {
			return $this->cache[$locale];
		}

		$locales = array_merge([$locale], $this->translatorLocalizer->getFallbackLocales());

		foreach ($locales as $loc) {
			$filename = sprintf(
				'%s/umpirsky/locale-list/data/%s/locales.php',
				$this->vendorDir,
				$loc
			);

			if (file_exists($filename)) {
				return $this->cache[$locale] = include $filename;
			}
		}

		throw new RuntimeException(sprintf(
			'Can\'t resolve the list of locales for locale %s.',
			$locale
		));
	}
}
