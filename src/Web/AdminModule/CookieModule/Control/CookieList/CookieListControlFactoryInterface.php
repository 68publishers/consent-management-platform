<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieList;

use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CookieListControlFactoryInterface
{
	/**
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider         $validLocalesProvider
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId|NULL $cookieProviderId
	 *
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl
	 */
	public function create(ValidLocalesProvider $validLocalesProvider, ?CookieProviderId $cookieProviderId = NULL): CookieListControl;
}
