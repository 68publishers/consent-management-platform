<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use Nette\Bridges\ApplicationLatte\Template;
use App\ReadModel\ConsentSettings\ConsentSettingsView;

final class ConsentSettingsDetailTemplate extends Template
{
	public ConsentSettingsView $consentSettingsView;
}
