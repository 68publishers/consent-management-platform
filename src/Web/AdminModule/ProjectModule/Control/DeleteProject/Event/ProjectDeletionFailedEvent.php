<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class ProjectDeletionFailedEvent extends Event
{
    public function __construct(
        private readonly Throwable $exception,
    ) {}

    public function exception(): Throwable
    {
        return $this->exception;
    }
}
