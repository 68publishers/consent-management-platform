<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Exception;

use DomainException;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

final class CookieProviderNotFoundException extends DomainException
{
	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $id
	 *
	 * @return static
	 */
	public static function withId(CookieProviderId $id): self
	{
		return new self(sprintf(
			'Cookie provider with ID %s not found.',
			$id
		));
	}
}
