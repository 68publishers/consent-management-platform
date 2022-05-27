<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

interface GlobalSettingsInterface
{
	/**
	 * @return \App\Application\GlobalSettings\Locale[]
	 */
	public function locales(): array;

	/**
	 * @return \App\Application\GlobalSettings\Locale
	 */
	public function defaultLocale(): Locale;

	/**
	 * @return void
	 */
	public function refresh(): void;
}
