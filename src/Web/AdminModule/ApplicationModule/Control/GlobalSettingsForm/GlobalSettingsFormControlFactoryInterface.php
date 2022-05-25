<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm;

interface GlobalSettingsFormControlFactoryInterface
{
	/**
	 * @return \App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\GlobalSettingsFormControl
	 */
	public function create(): GlobalSettingsFormControl;
}
