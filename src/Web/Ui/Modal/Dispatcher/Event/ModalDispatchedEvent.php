<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Dispatcher\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ModalDispatchedEvent extends Event
{
    public function __construct(
        private readonly string $name,
    ) {}

    public function name(): string
    {
        return $this->name;
    }
}
