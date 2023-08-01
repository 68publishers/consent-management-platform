<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ResetPassword;

use SixtyEightPublishers\ForgotPasswordBundle\Domain\ValueObject\PasswordRequestId;

interface ResetPasswordControlFactoryInterface
{
    public function create(PasswordRequestId $passwordRequestId): ResetPasswordControl;
}
