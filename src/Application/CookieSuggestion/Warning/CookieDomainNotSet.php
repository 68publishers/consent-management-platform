<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Warning;

final class CookieDomainNotSet implements WarningInterface
{
	public function getMessage(): string
	{
		return 'cookie_domain_not_set';
	}
}
