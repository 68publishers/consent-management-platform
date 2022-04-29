<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn;

interface SignInControlFactoryInterface
{
	/**
	 * @return \App\Web\FrontModule\Control\SignIn\SignInControl
	 */
	public function create(): SignInControl;
}
