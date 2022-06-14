<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieList;

use App\Application\GlobalSettings\Locale;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CookieListControlFactoryInterface
{
	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Application\GlobalSettings\Locale|NULL             $locale
	 *
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl
	 */
	public function create(CookieProviderId $cookieProviderId, ?Locale $locale): CookieListControl;
}
