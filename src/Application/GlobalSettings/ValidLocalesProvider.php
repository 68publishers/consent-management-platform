<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\Shared\ValueObject\LocalesConfig;
use App\Domain\Shared\ValueObject\Locale as LocaleValueObject;

final class ValidLocalesProvider
{
	private GlobalSettingsInterface $globalSettings;

	private ?LocalesConfig $localesConfig;

	/**
	 * @param \App\Application\GlobalSettings\GlobalSettingsInterface $globalSettings
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig|NULL       $localesConfig
	 */
	public function __construct(GlobalSettingsInterface $globalSettings, ?LocalesConfig $localesConfig = NULL)
	{
		$this->globalSettings = $globalSettings;
		$this->localesConfig = $localesConfig;
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig|NULL $localesConfig
	 *
	 * @return \App\Application\GlobalSettings\Locale[]
	 */
	public function getValidLocales(?LocalesConfig $localesConfig = NULL): array
	{
		$globalLocales = $this->globalSettings->locales();
		$localesConfig = $localesConfig ?? $this->localesConfig;

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
		$localesConfig = $localesConfig ?? $this->localesConfig;

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

	/**
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig|NULL $localesConfig
	 *
	 * @return $this
	 */
	public function withLocalesConfig(?LocalesConfig $localesConfig): self
	{
		return new self($this->globalSettings, $localesConfig);
	}
}
