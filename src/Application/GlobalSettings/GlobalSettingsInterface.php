<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

interface GlobalSettingsInterface
{
	/**
	 * [locale => name]
	 *
	 * @return array
	 */
	public function getNamedLocales(): array;
}
