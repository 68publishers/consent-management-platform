<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use App\ReadModel\Cookie\CookieView;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CookieFormModalControlFactoryInterface
{
	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\ReadModel\Cookie\CookieView|NULL                   $default
	 *
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl
	 */
	public function create(CookieProviderId $cookieProviderId, ?CookieView $default = NULL): CookieFormModalControl;
}
