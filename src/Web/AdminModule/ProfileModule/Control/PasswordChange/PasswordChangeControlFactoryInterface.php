<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\PasswordChange;

use App\ReadModel\User\UserView;

interface PasswordChangeControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\User\UserView $userView
	 *
	 * @return \App\Web\AdminModule\ProfileModule\Control\PasswordChange\PasswordChangeControl
	 */
	public function create(UserView $userView): PasswordChangeControl;
}
