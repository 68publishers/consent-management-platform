<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm;

use App\ReadModel\Category\CategoryView;

interface CategoryFormControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\Category\CategoryView|NULL $default
	 *
	 * @return \App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControl
	 */
	public function create(?CategoryView $default = NULL): CategoryFormControl;
}
