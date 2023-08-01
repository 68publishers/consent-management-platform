<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\ReadModel\ConsentSettings\ConsentSettingsView;

interface ConsentSettingsDetailModalControlFactoryInterface
{
    public function create(ConsentSettingsView $consentSettingsView): ConsentSettingsDetailModalControl;
}
