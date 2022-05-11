<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use DomainException;

final class InvalidColorException extends DomainException
{
	/**
	 * @param string $message
	 */
	private function __construct(string $message)
	{
		parent::__construct($message);
	}

	/**
	 * @param string $color
	 *
	 * @return static
	 */
	public static function invalidValue(string $color): self
	{
		return new self(sprintf(
			'Value %s is not valid color.',
			$color
		));
	}
}
