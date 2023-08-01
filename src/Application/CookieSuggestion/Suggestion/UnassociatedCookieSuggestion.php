<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Solution\Solutions;

final class UnassociatedCookieSuggestion extends AbstractSuggestion
{
    /** @var non-empty-list<ExistingCookie> */
    private array $existingCookies;

    private Solutions $solutions;

    /**
     * @param non-empty-list<CookieOccurrence> $occurrences
     * @param non-empty-list<ExistingCookie>   $existingCookies
     */
    public function __construct(
        string $suggestionId,
        string $suggestionName,
        string $suggestionDomain,
        array $occurrences,
        array $existingCookies,
        Solutions $solutions,
    ) {
        parent::__construct($suggestionId, false, $suggestionName, $suggestionDomain, $occurrences);

        $this->existingCookies = $existingCookies;
        $this->solutions = $solutions;
    }

    /**
     * @return non-empty-list<ExistingCookie>
     */
    public function getExistingCookies(): array
    {
        return $this->existingCookies;
    }

    public function getSolutions(): Solutions
    {
        return $this->solutions;
    }

    public function hasWarnings(): bool
    {
        foreach ($this->existingCookies as $existingCookie) {
            if (0 < count($existingCookie->warnings)) {
                return true;
            }
        }

        return false;
    }
}
