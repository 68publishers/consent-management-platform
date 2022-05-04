<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\PasswordRequestList;

interface PasswordRequestListControlFactoryInterface
{
	/**
	 * @return \App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControl
	 */
	public function create(): PasswordRequestListControl;
}
