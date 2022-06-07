<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderList;

interface ProviderListControlFactoryInterface
{
	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControl
	 */
	public function create(): ProviderListControl;
}
