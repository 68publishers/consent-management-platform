<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm;

interface LocalizationSettingsFormControlFactoryInterface
{
    public function create(): LocalizationSettingsFormControl;
}
