<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\UserList;

interface UserListControlFactoryInterface
{
	/**
	 * @return \App\Web\AdminModule\UserModule\Control\UserList\UserListControl
	 */
	public function create(): UserListControl;
}
