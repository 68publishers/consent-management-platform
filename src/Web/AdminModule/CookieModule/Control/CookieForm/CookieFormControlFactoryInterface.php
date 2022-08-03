<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use App\ReadModel\Cookie\CookieView;
use App\Application\GlobalSettings\ValidLocalesProvider;

interface CookieFormControlFactoryInterface
{
	/**
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider $validLocalesProvider
	 * @param \App\ReadModel\Cookie\CookieView|NULL                $default
	 *
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormControl
	 */
	public function create(ValidLocalesProvider $validLocalesProvider, ?CookieView $default = NULL): CookieFormControl;
}
