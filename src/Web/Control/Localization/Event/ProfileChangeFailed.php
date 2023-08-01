<?php

declare(strict_types=1);

namespace App\Web\Control\Localization\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class ProfileChangeFailed extends Event
{
    private Throwable $error;

    private string $profileCode;

    public function __construct(Throwable $error, string $profileCode)
    {
        $this->error = $error;
        $this->profileCode = $profileCode;
    }

    public function error(): Throwable
    {
        return $this->error;
    }

    public function profileCode(): string
    {
        return $this->profileCode;
    }
}
