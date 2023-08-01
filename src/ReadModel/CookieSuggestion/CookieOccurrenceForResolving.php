<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use DateTimeImmutable;

final class CookieOccurrenceForResolving
{
    /**
     * @param array<int, string> $acceptedCategories
     */
    public function __construct(
        public string $id,
        public string $scenarioName,
        public string $cookieName,
        public string $foundOnUrl,
        public array $acceptedCategories,
        public DateTimeImmutable $lastFoundAt,
    ) {}
}
