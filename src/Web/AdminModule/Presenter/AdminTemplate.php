<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\Web\Ui\DefaultPresenterTemplate;
use SixtyEightPublishers\UserBundle\ReadModel\View\IdentityView;

abstract class AdminTemplate extends DefaultPresenterTemplate
{
	public IdentityView $identity;
}
