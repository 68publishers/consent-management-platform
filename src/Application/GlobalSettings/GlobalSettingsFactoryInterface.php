<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

interface GlobalSettingsFactoryInterface
{
	/**
	 * @return \App\Application\GlobalSettings\GlobalSettingsInterface
	 */
	public function create(): GlobalSettingsInterface;
}
