<?php

declare(strict_types=1);

namespace App\Domain\Category\Exception;

use DomainException;

final class InvalidCodeException extends DomainException
{
	/**
	 * @param string $message
	 */
	private function __construct(string $message)
	{
		parent::__construct($message);
	}

	/**
	 * @param string $code
	 * @param int    $maxLength
	 *
	 * @return static
	 */
	public static function tooLong(string $code, int $maxLength): self
	{
		return new self(sprintf(
			'Code "%s" is too long, maximal allowed length is %d characters.',
			$code,
			$maxLength
		));
	}
}
