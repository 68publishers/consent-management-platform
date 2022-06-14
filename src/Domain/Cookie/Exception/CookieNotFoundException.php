<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Exception;

use DomainException;
use App\Domain\Cookie\ValueObject\CookieId;

final class CookieNotFoundException extends DomainException
{
	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId $id
	 *
	 * @return static
	 */
	public static function withId(CookieId $id): self
	{
		return new self(sprintf(
			'Cookie with ID %s not found.',
			$id
		));
	}
}
