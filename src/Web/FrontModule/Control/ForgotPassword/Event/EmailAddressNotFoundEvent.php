<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ForgotPassword\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class EmailAddressNotFoundEvent extends Event
{
    public function __construct(
        private readonly string $emailAddress,
    ) {}

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }
}
