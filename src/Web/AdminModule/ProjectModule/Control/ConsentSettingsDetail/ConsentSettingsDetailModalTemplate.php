<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\Web\Ui\Modal\AbstractModalTemplate;

final class ConsentSettingsDetailModalTemplate extends AbstractModalTemplate
{
    public ConsentSettingsView $consentSettingsView;
}
