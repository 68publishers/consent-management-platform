<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class ImportFormProcessingFailedEvent extends Event
{
    public function __construct(
        private readonly Throwable $error,
    ) {}

    public function getError(): Throwable
    {
        return $this->error;
    }
}
