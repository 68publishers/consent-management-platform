<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Exception;

use DomainException;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;

final class CookieSuggestionNotFoundException extends DomainException
{
	public static function byId(CookieSuggestionId $id): self
	{
		return new self(sprintf(
			'Cookie suggestion with ID %s not found.',
			$id
		));
	}
}
