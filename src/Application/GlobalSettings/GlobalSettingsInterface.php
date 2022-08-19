<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\GlobalSettings\ValueObject\ApiCache;

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
	 * @return \App\Domain\GlobalSettings\ValueObject\ApiCache
	 */
	public function apiCache(): ApiCache;

	/**
	 * @return void
	 */
	public function refresh(): void;
}
