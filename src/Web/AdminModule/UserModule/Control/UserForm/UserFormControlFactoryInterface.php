<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\UserForm;

use App\ReadModel\User\UserView;

interface UserFormControlFactoryInterface
{
    public function create(?UserView $default = null): UserFormControl;
}
