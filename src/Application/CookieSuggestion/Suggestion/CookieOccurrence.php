<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\ReadModel\CookieSuggestion\CookieOccurrenceForResolving;
use DateTimeImmutable;

final class CookieOccurrence
{
    public string $id;

    public string $scenarioName;

    public string $cookieName;

    public string $foundOnUrl;

    /** @var array<int, string> */
    public array $acceptedCategories;

    public DateTimeImmutable $lastFoundAt;

    /**
     * @param array<int, string> $acceptedCategories
     */
    public function __construct(
        string $id,
        string $scenarioName,
        string $cookieName,
        string $foundOnUrl,
        array $acceptedCategories,
        DateTimeImmutable $lastFoundAt,
    ) {
        $this->id = $id;
        $this->scenarioName = $scenarioName;
        $this->cookieName = $cookieName;
        $this->foundOnUrl = $foundOnUrl;
        $this->acceptedCategories = $acceptedCategories;
        $this->lastFoundAt = $lastFoundAt;
    }

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
