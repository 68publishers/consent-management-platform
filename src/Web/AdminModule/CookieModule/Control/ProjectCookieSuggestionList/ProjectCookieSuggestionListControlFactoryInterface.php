<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProjectCookieSuggestionList;

interface ProjectCookieSuggestionListControlFactoryInterface
{
	public function create(): ProjectCookieSuggestionListControl;
}
