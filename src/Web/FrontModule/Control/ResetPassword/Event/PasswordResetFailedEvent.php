<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ResetPassword\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class PasswordResetFailedEvent extends Event
{
    public function __construct(
        private readonly Throwable $exception,
    ) {}

    public function exception(): Throwable
    {
        return $this->exception;
    }
}
