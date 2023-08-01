<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\ReadModel\CookieSuggestion\CookieOccurrenceForResolving;
use DateTimeImmutable;

final class CookieOccurrence
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

    public static function fromCookieOccurrenceForResolving(CookieOccurrenceForResolving $cookieOccurrenceForResolving): self
    {
        return new self(
            $cookieOccurrenceForResolving->id,
            $cookieOccurrenceForResolving->scenarioName,
            $cookieOccurrenceForResolving->cookieName,
            $cookieOccurrenceForResolving->foundOnUrl,
            $cookieOccurrenceForResolving->acceptedCategories,
            $cookieOccurrenceForResolving->lastFoundAt,
        );
    }
}
