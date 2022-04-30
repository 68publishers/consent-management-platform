<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ForgotPassword;

interface ForgotPasswordControlFactoryInterface
{
	/**
	 * @return \App\Web\FrontModule\Control\ForgotPassword\ForgotPasswordControl
	 */
	public function create(): ForgotPasswordControl;
}
