<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use App\Application\GlobalSettings\ValidLocalesProvider;
use App\ReadModel\Cookie\CookieView;

interface CookieFormControlFactoryInterface
{
    public function create(ValidLocalesProvider $validLocalesProvider, ?CookieView $default = null): CookieFormControl;
}
