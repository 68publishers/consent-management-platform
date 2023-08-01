<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\UserList;

interface UserListControlFactoryInterface
{
    public function create(): UserListControl;
}
