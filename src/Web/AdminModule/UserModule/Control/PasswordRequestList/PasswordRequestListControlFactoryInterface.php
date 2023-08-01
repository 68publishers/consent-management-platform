<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\PasswordRequestList;

interface PasswordRequestListControlFactoryInterface
{
    public function create(): PasswordRequestListControl;
}
