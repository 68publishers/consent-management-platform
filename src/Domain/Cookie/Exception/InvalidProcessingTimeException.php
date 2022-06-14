<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Exception;

use DomainException;
use App\Domain\Cookie\ValueObject\ProcessingTime;

final class InvalidProcessingTimeException extends DomainException
{
	/**
	 * @param string $message
	 */
	private function __construct(string $message)
	{
		parent::__construct($message);
	}

	/**
	 * @param string $value
	 *
	 * @return static
	 */
	public static function invalidValue(string $value): self
	{
		return new self(sprintf(
			'Processing time must be "%s" or "%s" or valid estimate mask. String "%s" given.',
			ProcessingTime::PERSISTENT,
			ProcessingTime::SESSION,
			$value
		));
	}
}
