<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Logger;

use App\Application\DataProcessor\Read\Event\ReaderErrorEvent;
use Psr\Log\LoggerInterface;

final class PsrErrorLogger
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(ReaderErrorEvent $event): void
    {
        $this->logger->error($event->error()->getMessage());
    }
}
