<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Event;

use App\Application\DataProcessor\Exception\DataReaderExceptionInterface;

final class ReaderErrorEvent
{
    private bool $stopped = false;

    public function __construct(
        private readonly DataReaderExceptionInterface $error,
    ) {}

    public function error(): DataReaderExceptionInterface
    {
        return $this->error;
    }

    public function stopped(): bool
    {
        return $this->stopped;
    }

    public function stop(): self
    {
        $this->stopped = true;

        return $this;
    }
}
