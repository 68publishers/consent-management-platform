<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\ReadModel\ConsentSettings\ConsentSettingsView;
use Nette\Bridges\ApplicationLatte\Template;

final class ConsentSettingsDetailTemplate extends Template
{
    public ConsentSettingsView $consentSettingsView;
}
