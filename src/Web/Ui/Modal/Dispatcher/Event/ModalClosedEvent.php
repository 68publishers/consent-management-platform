<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Dispatcher\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ModalClosedEvent extends Event
{
    public function __construct(
        private readonly array $names,
    ) {}

    public function names(): array
    {
        return $this->names;
    }
}
