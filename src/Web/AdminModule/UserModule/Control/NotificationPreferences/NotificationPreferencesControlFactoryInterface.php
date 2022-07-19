<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\NotificationPreferences;

use App\ReadModel\User\UserView;

interface NotificationPreferencesControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\User\UserView $userVIew
	 *
	 * @return \App\Web\AdminModule\UserModule\Control\NotificationPreferences\NotificationPreferencesControl
	 */
	public function create(UserView $userVIew): NotificationPreferencesControl;
}
