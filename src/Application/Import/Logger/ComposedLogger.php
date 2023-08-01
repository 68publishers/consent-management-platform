<?php

declare(strict_types=1);

namespace App\Application\Import\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

final class ComposedLogger extends AbstractLogger
{
    /** @var LoggerInterface[] */
    private array $loggers;

    public function __construct(LoggerInterface ...$loggers)
    {
        $this->loggers = $loggers;
    }

    public function log($level, $message, array $context = []): void
    {
        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
