<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\Web\Ui\DefaultPresenterTemplate;
use App\Application\GlobalSettings\Locale;
use SixtyEightPublishers\UserBundle\ReadModel\View\IdentityView;

abstract class AdminTemplate extends DefaultPresenterTemplate
{
	public IdentityView $identity;

	/** @var \App\Application\GlobalSettings\Locale[] */
	public array $locales;

	public ?Locale $defaultLocale = NULL;
}
