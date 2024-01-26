<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\AzureAuthSettingsForm;

interface AzureAuthSettingsFormControlFactoryInterface
{
    public function create(): AzureAuthSettingsFormControl;
}
