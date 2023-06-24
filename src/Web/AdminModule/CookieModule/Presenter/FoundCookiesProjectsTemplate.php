<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Web\AdminModule\Presenter\AdminTemplate;
use App\ReadModel\Project\FoundCookieProjectListingItem;

final class FoundCookiesProjectsTemplate extends AdminTemplate
{
	/** @var array<FoundCookieProjectListingItem>  */
	public array $projects;
}
