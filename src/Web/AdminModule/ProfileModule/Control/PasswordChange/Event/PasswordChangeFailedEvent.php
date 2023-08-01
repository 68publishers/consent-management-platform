<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\PasswordChange\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class PasswordChangeFailedEvent extends Event
{
    public function __construct(
        private readonly Throwable $error,
    ) {}

    public function getError(): Throwable
    {
        return $this->error;
    }
}
