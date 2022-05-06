<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation;

use SixtyEightPublishers\UserBundle\ReadModel\View\UserView;

interface BasicInformationControlFactoryInterface
{
	/**
	 * @param \SixtyEightPublishers\UserBundle\ReadModel\View\UserView $userView
	 *
	 * @return \App\Web\AdminModule\ProfileModule\Control\BasicInformation\BasicInformationControl
	 */
	public function create(UserView $userView): BasicInformationControl;
}
