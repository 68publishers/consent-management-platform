<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\UserBundle\ReadModel\View\UserView;

final class BasicInformationTemplate extends Template
{
	public UserView $userView;
}
