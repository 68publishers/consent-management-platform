<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class ProjectDeletionFailedEvent extends Event
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
