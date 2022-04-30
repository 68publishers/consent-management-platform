<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ResetPassword;

use SixtyEightPublishers\ForgotPasswordBundle\Domain\Dto\PasswordRequestId;

interface ResetPasswordControlFactoryInterface
{
	/**
	 * @param \SixtyEightPublishers\ForgotPasswordBundle\Domain\Dto\PasswordRequestId $passwordRequestId
	 *
	 * @return \App\Web\FrontModule\Control\ResetPassword\ResetPasswordControl
	 */
	public function create(PasswordRequestId $passwordRequestId): ResetPasswordControl;
}
