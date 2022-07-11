<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\Web\Ui\Modal\AbstractModalTemplate;
use App\ReadModel\ConsentSettings\ConsentSettingsView;

final class ConsentSettingsDetailModalTemplate extends AbstractModalTemplate
{
	public ConsentSettingsView $consentSettingsView;
}
