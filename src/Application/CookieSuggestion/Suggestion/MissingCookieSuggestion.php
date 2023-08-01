<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Solution\Solutions;

final class MissingCookieSuggestion extends AbstractSuggestion
{
    /**
     * @param non-empty-list<CookieOccurrence> $occurrences
     */
    public function __construct(
        string $suggestionId,
        string $suggestionName,
        string $suggestionDomain,
        array $occurrences,
        public Solutions $solutions,
    ) {
        parent::__construct($suggestionId, false, $suggestionName, $suggestionDomain, $occurrences);
    }

    public function getSolutions(): Solutions
    {
        return $this->solutions;
    }
}
