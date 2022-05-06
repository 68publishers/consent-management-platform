<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\UserForm;

use SixtyEightPublishers\UserBundle\ReadModel\View\UserView;

interface UserFormControlFactoryInterface
{
	/**
	 * @param \SixtyEightPublishers\UserBundle\ReadModel\View\UserView|NULL $default
	 *
	 * @return \App\Web\AdminModule\UserModule\Control\UserForm\UserFormControl
	 */
	public function create(?UserView $default = NULL): UserFormControl;
}
