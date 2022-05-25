<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings;

interface GlobalSettingsRepositoryInterface
{
	/**
	 * @param \App\Domain\GlobalSettings\GlobalSettings $globalSettings
	 *
	 * @return void
	 */
	public function save(GlobalSettings $globalSettings): void;

	/**
	 * Singleton!
	 *
	 * @return \App\Domain\ConsentSettings\ConsentSettings|NULL
	 */
	public function get(): ?GlobalSettings;
}
