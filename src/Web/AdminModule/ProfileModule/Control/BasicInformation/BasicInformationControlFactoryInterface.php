<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation;

use App\ReadModel\User\UserView;

interface BasicInformationControlFactoryInterface
{
    public function create(UserView $userView): BasicInformationControl;
}
