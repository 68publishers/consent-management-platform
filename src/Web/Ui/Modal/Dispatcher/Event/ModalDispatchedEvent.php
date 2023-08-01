<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Dispatcher\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ModalDispatchedEvent extends Event
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
