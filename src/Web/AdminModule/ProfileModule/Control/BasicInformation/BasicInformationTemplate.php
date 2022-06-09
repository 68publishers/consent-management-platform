<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation;

use App\ReadModel\User\UserView;
use Nette\Bridges\ApplicationLatte\Template;

final class BasicInformationTemplate extends Template
{
	public UserView $userView;
}
