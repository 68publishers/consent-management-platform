<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportForm\Event;

use App\Application\Import\ImportState;
use Symfony\Contracts\EventDispatcher\Event;

final class ImportEndedEvent extends Event
{
    public function __construct(
        private readonly ImportState $importState,
    ) {}

    public function importState(): ImportState
    {
        return $this->importState;
    }
}
