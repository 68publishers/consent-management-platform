<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Warning;

final class CookieDoesNotHaveSameDomain implements WarningInterface
{
    public function getMessage(): string
    {
        return 'cookie_does_not_have_same_domain';
    }
}
