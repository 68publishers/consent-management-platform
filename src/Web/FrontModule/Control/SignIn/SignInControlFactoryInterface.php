<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn;

interface SignInControlFactoryInterface
{
    public function create(): SignInControl;
}
