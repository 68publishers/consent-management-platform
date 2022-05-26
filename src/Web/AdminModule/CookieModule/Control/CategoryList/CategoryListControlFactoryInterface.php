<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryList;

interface CategoryListControlFactoryInterface
{
	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControl
	 */
	public function create(): CategoryListControl;
}
