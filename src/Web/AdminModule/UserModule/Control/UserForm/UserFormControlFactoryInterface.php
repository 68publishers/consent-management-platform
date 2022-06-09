<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\UserForm;

use App\ReadModel\User\UserView;

interface UserFormControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\User\UserView|NULL $default
	 *
	 * @return \App\Web\AdminModule\UserModule\Control\UserForm\UserFormControl
	 */
	public function create(?UserView $default = NULL): UserFormControl;
}
