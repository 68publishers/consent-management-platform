<?php

declare(strict_types=1);

namespace App\Application\Import\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

final class ComposedLogger extends AbstractLogger
{
	/** @var \Psr\Log\LoggerInterface[]  */
	private array $loggers;

	/**
	 * @param \Psr\Log\LoggerInterface ...$loggers
	 */
	public function __construct(LoggerInterface ...$loggers)
	{
		$this->loggers = $loggers;
	}

	/**
	 * {@inheritDoc}
	 */
	public function log($level, $message, array $context = []): void
	{
		foreach ($this->loggers as $logger) {
			$logger->log($level, $message, $context);
		}
	}
}
