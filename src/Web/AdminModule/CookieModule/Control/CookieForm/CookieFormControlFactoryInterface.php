<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use App\ReadModel\Cookie\CookieView;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CookieFormControlFactoryInterface
{
	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\ReadModel\Cookie\CookieView|NULL                   $default
	 *
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormControl
	 */
	public function create(CookieProviderId $cookieProviderId, ?CookieView $default = NULL): CookieFormControl;
}
