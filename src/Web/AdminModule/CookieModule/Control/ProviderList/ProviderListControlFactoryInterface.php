<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderList;

interface ProviderListControlFactoryInterface
{
    public function create(): ProviderListControl;
}
