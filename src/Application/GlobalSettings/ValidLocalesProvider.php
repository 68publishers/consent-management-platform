<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\Shared\ValueObject\LocalesConfig;
use App\Domain\Shared\ValueObject\Locale as LocaleValueObject;

final class ValidLocalesProvider
{
	private GlobalSettingsInterface $globalSettings;

	/**
	 * @param \App\Application\GlobalSettings\GlobalSettingsInterface $globalSettings
	 */
	public function __construct(GlobalSettingsInterface $globalSettings)
	{
		$this->globalSettings = $globalSettings;
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig|NULL $localesConfig
	 *
	 * @return \App\Application\GlobalSettings\Locale[]
	 */
	public function getValidLocales(?LocalesConfig $localesConfig = NULL): array
	{
		$globalLocales = $this->globalSettings->locales();

		if (NULL === $localesConfig) {
			return $globalLocales;
		}

		$validLocales = [];

		foreach ($localesConfig->locales()->all() as $localeVo) {
			assert($localeVo instanceof LocaleValueObject);

			foreach ($globalLocales as $globalLocale) {
				if ($localeVo->value() === $globalLocale->code()) {
					$validLocales[] = $globalLocale;

					continue 2;
				}
			}
		}

		return $validLocales;
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig|NULL $localesConfig
	 *
	 * @return \App\Application\GlobalSettings\Locale|NULL
	 */
	public function getValidDefaultLocale(?LocalesConfig $localesConfig = NULL): ?Locale
	{
		if (NULL === $localesConfig) {
			$defaultGlobalLocale = $this->globalSettings->defaultLocale();

			return 'unknown' !== $defaultGlobalLocale->code() ? $defaultGlobalLocale : NULL;
		}

		$defaultLocale = $localesConfig->defaultLocale();

		foreach ($this->globalSettings->locales() as $globalLocale) {
			if ($defaultLocale->value() === $globalLocale->code()) {
				return $globalLocale;
			}
		}

		return NULL;
	}
}
