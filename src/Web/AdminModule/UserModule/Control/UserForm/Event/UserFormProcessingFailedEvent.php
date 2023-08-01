<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\UserForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class UserFormProcessingFailedEvent extends Event
{
    private Throwable $error;

    public function __construct(Throwable $error)
    {
        $this->error = $error;
    }

    public function getError(): Throwable
    {
        return $this->error;
    }
}
