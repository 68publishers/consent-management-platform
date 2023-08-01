<?php

declare(strict_types=1);

namespace App\Application\Import\Logger;

use App\Application\Import\ImportState;
use Psr\Log\AbstractLogger;

final class ImportLogger extends AbstractLogger
{
    private ImportState $state;

    public function __construct(ImportState $state)
    {
        $this->state = $state;
    }

    public function log($level, $message, array $context = []): void
    {
        if (!empty($this->state->output)) {
            $this->state->output .= "\n";
        }

        $this->state->output .= sprintf('[%s] %s', $level, $message);
    }
}
