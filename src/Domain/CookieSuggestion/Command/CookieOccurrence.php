<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Command;

final class CookieOccurrence
{
    /**
     * @param array<int, string> $acceptedCategories
     */
    public function __construct(
        public string $scenarioName,
        public string $foundOnUrl,
        public array $acceptedCategories,
        public string $lastFoundAt,
    ) {}
}
