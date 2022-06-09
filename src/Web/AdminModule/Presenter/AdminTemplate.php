<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\ReadModel\User\UserView;
use App\Web\Ui\DefaultPresenterTemplate;
use App\Application\GlobalSettings\Locale;

abstract class AdminTemplate extends DefaultPresenterTemplate
{
	public UserView $identity;

	/** @var \App\Application\GlobalSettings\Locale[] */
	public array $locales;

	public ?Locale $defaultLocale = NULL;
}
