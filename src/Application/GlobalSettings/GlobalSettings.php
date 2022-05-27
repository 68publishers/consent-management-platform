<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

final class GlobalSettings implements GlobalSettingsInterface
{
	private array $locales;

	private Locale $defaultLocale;

	/**
	 * @param array                                  $locales
	 * @param \App\Application\GlobalSettings\Locale $defaultLocale
	 */
	public function __construct(array $locales, Locale $defaultLocale)
	{
		$this->locales = $locales;
		$this->defaultLocale = $defaultLocale;
	}

	/**
	 * @return static
	 */
	public static function default(): self
	{
		return new self([], Locale::unknown());
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
	public function refresh(): void
	{
	}
}
