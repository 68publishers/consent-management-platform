<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class ImportFormProcessingFailedEvent extends Event
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
