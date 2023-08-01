<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class ProjectFormProcessingFailedEvent extends Event
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
