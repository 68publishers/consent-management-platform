<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Problem\ProblemInterface;

final class ProblematicCookieSuggestion extends AbstractSuggestion
{
    /**
     * @param non-empty-list<CookieOccurrence> $occurrences
     * @param non-empty-list<ExistingCookie>   $existingCookies
     * @param non-empty-list<ProblemInterface> $problems
     */
    public function __construct(
        string $suggestionId,
        string $suggestionName,
        string $suggestionDomain,
        array $occurrences,
        private readonly array $existingCookies,
        public array $problems,
        bool $virtual = false,
    ) {
        parent::__construct($suggestionId, $virtual, $suggestionName, $suggestionDomain, $occurrences);
    }

    /**
     * @return non-empty-list<ExistingCookie>
     */
    public function getExistingCookies(): array
    {
        return $this->existingCookies;
    }

    /**
     * @return non-empty-list<ProblemInterface>
     */
    public function getProblems(): array
    {
        return $this->problems;
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
