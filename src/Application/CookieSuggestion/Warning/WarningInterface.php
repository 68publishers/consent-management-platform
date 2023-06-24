<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Warning;

interface WarningInterface
{
	public function getMessage(): string;
}
