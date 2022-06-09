<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderForm;

use App\ReadModel\CookieProvider\CookieProviderView;

interface ProviderFormControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\CookieProvider\CookieProviderView|NULL $default
	 *
	 * @return \App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControl
	 */
	public function create(?CookieProviderView $default = NULL): ProviderFormControl;
}
