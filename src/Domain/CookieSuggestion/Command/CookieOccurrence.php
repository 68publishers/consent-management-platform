<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Command;

final class CookieOccurrence
{
    public string $scenarioName;

    public string $foundOnUrl;

    /** @var array<int, string> */
    public array $acceptedCategories;

    public string $lastFoundAt;

    /**
     * @param array<int, string> $acceptedCategories
     */
    public function __construct(
        string $scenarioName,
        string $foundOnUrl,
        array $acceptedCategories,
        string $lastFoundAt,
    ) {
        $this->scenarioName = $scenarioName;
        $this->foundOnUrl = $foundOnUrl;
        $this->acceptedCategories = $acceptedCategories;
        $this->lastFoundAt = $lastFoundAt;
    }
}
