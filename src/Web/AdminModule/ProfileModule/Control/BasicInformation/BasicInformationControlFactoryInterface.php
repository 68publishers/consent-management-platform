<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation;

use App\ReadModel\User\UserView;

interface BasicInformationControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\User\UserView $userView
	 *
	 * @return \App\Web\AdminModule\ProfileModule\Control\BasicInformation\BasicInformationControl
	 */
	public function create(UserView $userView): BasicInformationControl;
}
