<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\Presenter\AdminTemplate;
use App\ReadModel\CookieProvider\CookieProviderView;

final class EditProviderTemplate extends AdminTemplate
{
	public CookieProviderView $cookieProviderView;

	public ?ProjectView $projectView = NULL;
}
