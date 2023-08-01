<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieList;

use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CookieListControlFactoryInterface
{
    public function create(ValidLocalesProvider $validLocalesProvider, ?CookieProviderId $cookieProviderId = null): CookieListControl;
}
