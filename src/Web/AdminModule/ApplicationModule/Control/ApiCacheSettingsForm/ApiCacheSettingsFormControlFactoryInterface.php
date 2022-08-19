<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\ApiCacheSettingsForm;

interface ApiCacheSettingsFormControlFactoryInterface
{
	/**
	 * @return \App\Web\AdminModule\ApplicationModule\Control\ApiCacheSettingsForm\ApiCacheSettingsFormControl
	 */
	public function create(): ApiCacheSettingsFormControl;
}
