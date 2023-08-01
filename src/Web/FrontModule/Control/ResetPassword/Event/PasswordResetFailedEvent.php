<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ResetPassword\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class PasswordResetFailedEvent extends Event
{
    private Throwable $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function exception(): Throwable
    {
        return $this->exception;
    }
}
