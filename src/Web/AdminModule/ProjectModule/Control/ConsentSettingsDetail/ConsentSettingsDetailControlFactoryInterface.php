<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\ReadModel\ConsentSettings\ConsentSettingsView;

interface ConsentSettingsDetailControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\ConsentSettings\ConsentSettingsView $consentSettingsView
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailControl
	 */
	public function create(ConsentSettingsView $consentSettingsView): ConsentSettingsDetailControl;
}
