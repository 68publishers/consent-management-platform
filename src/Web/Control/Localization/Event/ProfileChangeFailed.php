<?php

declare(strict_types=1);

namespace App\Web\Control\Localization\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class ProfileChangeFailed extends Event
{
    public function __construct(
        private readonly Throwable $error,
        private readonly string $profileCode,
    ) {}

    public function error(): Throwable
    {
        return $this->error;
    }

    public function profileCode(): string
    {
        return $this->profileCode;
    }
}
