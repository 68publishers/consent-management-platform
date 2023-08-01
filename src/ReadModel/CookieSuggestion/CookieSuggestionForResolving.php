<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use DateTimeImmutable;

final class CookieSuggestionForResolving
{
    /**
     * @param array<CookieOccurrenceForResolving> $occurrences
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $domain,
        public DateTimeImmutable $createdAt,
        public bool $ignoredUntilNextOccurrence,
        public bool $ignoredPermanently,
        public array $occurrences,
    ) {}

    public function isIgnored(): bool
    {
        return $this->ignoredUntilNextOccurrence || $this->ignoredPermanently;
    }

    public function withName(string $name): self
    {
        return new self(
            $this->id,
            $name,
            $this->domain,
            $this->createdAt,
            $this->ignoredUntilNextOccurrence,
            $this->ignoredPermanently,
            $this->occurrences,
        );
    }

    /**
     * @param array<CookieOccurrenceForResolving> $occurrences
     */
    public function mergeOccurrences(array $occurrences): self
    {
        $keys = array_map(
            static fn (CookieOccurrenceForResolving $occurrenceForResolving): string => $occurrenceForResolving->scenarioName . '__x__' . $occurrenceForResolving->cookieName,
            $this->occurrences,
        );
        $currentOccurrences = array_combine($keys, $this->occurrences);

        foreach ($occurrences as $occurrence) {
            $key = $occurrence->scenarioName . '__x__' . $occurrence->cookieName;

            if (!isset($currentOccurrences[$key]) || $occurrence->lastFoundAt > $currentOccurrences[$key]->lastFoundAt) {
                $currentOccurrences[$key] = $occurrence;
            }
        }

        usort(
            $currentOccurrences,
            static fn (CookieOccurrenceForResolving $left, CookieOccurrenceForResolving $right): int =>
            $right->lastFoundAt <=> $left->lastFoundAt,
        );

        return new self(
            $this->id,
            $this->name,
            $this->domain,
            $this->createdAt,
            $this->ignoredUntilNextOccurrence,
            $this->ignoredPermanently,
            array_values($currentOccurrences),
        );
    }
}
