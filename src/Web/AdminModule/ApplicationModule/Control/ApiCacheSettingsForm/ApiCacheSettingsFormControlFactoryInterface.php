<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\ApiCacheSettingsForm;

interface ApiCacheSettingsFormControlFactoryInterface
{
    public function create(): ApiCacheSettingsFormControl;
}
