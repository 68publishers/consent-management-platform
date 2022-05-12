<?php

declare(strict_types=1);

namespace App\Domain\Consent\Exception;

use DomainException;
use App\Domain\Consent\ValueObject\ConsentId;

final class ConsentNotFoundException extends DomainException
{
	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId $id
	 *
	 * @return static
	 */
	public static function withId(ConsentId $id): self
	{
		return new self(sprintf(
			'Consent with ID %s not found.',
			$id
		));
	}
}
