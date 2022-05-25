<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

final class GlobalSettings implements GlobalSettingsInterface
{
	private array $locales;

	/**
	 * @param array $locales
	 */
	public function __construct(array $locales)
	{
		$this->locales = $locales;
	}

	/**
	 * @return static
	 */
	public static function default(): self
	{
		return new self([]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getNamedLocales(): array
	{
		return $this->locales;
	}
}
