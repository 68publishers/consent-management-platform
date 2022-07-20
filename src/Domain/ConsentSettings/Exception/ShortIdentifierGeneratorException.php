<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Exception;

use Throwable;
use DomainException;

final class ShortIdentifierGeneratorException extends DomainException
{
	/**
	 * @param \Throwable $e
	 *
	 * @return static
	 */
	public static function from(Throwable $e): self
	{
		return new self(sprintf(
			'Can\'t generate short identifier: %s',
			$e->getMessage()
		), $e->getCode(), $e);
	}
}
