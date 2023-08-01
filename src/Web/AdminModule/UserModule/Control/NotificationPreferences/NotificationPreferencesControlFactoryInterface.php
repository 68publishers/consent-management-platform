<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\NotificationPreferences;

use App\ReadModel\User\UserView;

interface NotificationPreferencesControlFactoryInterface
{
    public function create(UserView $userVIew): NotificationPreferencesControl;
}
