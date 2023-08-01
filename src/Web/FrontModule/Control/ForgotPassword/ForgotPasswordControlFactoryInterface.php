<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ForgotPassword;

interface ForgotPasswordControlFactoryInterface
{
    public function create(): ForgotPasswordControl;
}
