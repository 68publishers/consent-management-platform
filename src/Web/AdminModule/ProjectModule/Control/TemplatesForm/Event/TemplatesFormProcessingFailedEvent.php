<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class TemplatesFormProcessingFailedEvent extends Event
{
    public function __construct(
        private readonly Throwable $error,
    ) {}

    public function getError(): Throwable
    {
        return $this->error;
    }
}
