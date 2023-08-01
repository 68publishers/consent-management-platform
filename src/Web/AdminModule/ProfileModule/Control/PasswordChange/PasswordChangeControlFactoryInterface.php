<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\PasswordChange;

use App\ReadModel\User\UserView;

interface PasswordChangeControlFactoryInterface
{
    public function create(UserView $userView): PasswordChangeControl;
}
