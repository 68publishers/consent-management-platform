<?php

declare(strict_types=1);

namespace App\Application\Import\Logger;

use Psr\Log\AbstractLogger;
use App\Application\Import\ImportState;

final class ImportLogger extends AbstractLogger
{
	private ImportState $state;

	/**
	 * @param \App\Application\Import\ImportState $state
	 */
	public function __construct(ImportState $state)
	{
		$this->state = $state;
	}

	/**
	 * {@inheritDoc}
	 */
	public function log($level, $message, array $context = []): void
	{
		if (!empty($this->state->output)) {
			$this->state->output .= "\n";
		}

		$this->state->output .= sprintf('[%s] %s', $level, $message);
	}
}
