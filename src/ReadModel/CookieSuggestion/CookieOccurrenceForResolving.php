<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use DateTimeImmutable;

final class CookieOccurrenceForResolving
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
}
