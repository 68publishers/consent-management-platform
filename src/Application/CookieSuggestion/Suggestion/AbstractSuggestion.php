<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

abstract class AbstractSuggestion implements SuggestionInterface
{
    /**
     * @param non-empty-list<CookieOccurrence> $occurrences
     */
    public function __construct(
        private readonly string $suggestionId,
        private readonly bool $virtual,
        private readonly string $suggestionName,
        private readonly string $suggestionDomain,
        private readonly array $occurrences,
    ) {}

    public function getSuggestionId(): string
    {
        return $this->suggestionId;
    }

    public function getSuggestionName(): string
    {
        return $this->suggestionName;
    }

    public function getSuggestionDomain(): string
    {
        return $this->suggestionDomain;
    }

    public function getOccurrences(): array
    {
        return $this->occurrences;
    }

    public function hasWarnings(): bool
    {
        return false;
    }

    public function isVirtual(): bool
    {
        return $this->virtual;
    }

    public function getLatestOccurrence(): ?CookieOccurrence
    {
        $occurrences = $this->occurrences;

        if (0 >= count($occurrences)) {
            return null;
        }

        usort($occurrences, static fn (CookieOccurrence $left, CookieOccurrence $right) => $right->lastFoundAt <=> $left->lastFoundAt);

        return array_shift($occurrences);
    }
}
