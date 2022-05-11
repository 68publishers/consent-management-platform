<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use DomainException;

final class CodeUniquenessException extends DomainException
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
	 *
	 * @return static
	 */
	public static function create(string $code): self
	{
		return new self(sprintf(
			'Project with a code "%s" already exists.',
			$code
		));
	}
}
