<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryList;

use App\Application\GlobalSettings\Locale;

interface CategoryListControlFactoryInterface
{
	/**
	 * @param \App\Application\GlobalSettings\Locale|NULL $locale
	 *
	 * @return \App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControl
	 */
	public function create(?Locale $locale): CategoryListControl;
}
