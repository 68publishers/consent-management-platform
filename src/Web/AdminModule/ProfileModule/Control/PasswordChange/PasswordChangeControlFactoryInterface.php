<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\PasswordChange;

use SixtyEightPublishers\UserBundle\ReadModel\View\UserView;

interface PasswordChangeControlFactoryInterface
{
	/**
	 * @param \SixtyEightPublishers\UserBundle\ReadModel\View\UserView $userView
	 *
	 * @return \App\Web\AdminModule\ProfileModule\Control\PasswordChange\PasswordChangeControl
	 */
	public function create(UserView $userView): PasswordChangeControl;
}
