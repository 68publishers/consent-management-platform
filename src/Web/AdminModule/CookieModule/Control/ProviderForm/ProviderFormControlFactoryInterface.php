<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderForm;

use App\ReadModel\CookieProvider\CookieProviderView;

interface ProviderFormControlFactoryInterface
{
    public function create(?CookieProviderView $default = null): ProviderFormControl;
}
