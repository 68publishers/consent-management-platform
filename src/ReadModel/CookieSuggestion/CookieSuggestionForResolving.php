<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use DateTimeImmutable;

final class CookieSuggestionForResolving
{
    public string $id;

    public string $name;

    public string $domain;

    public DateTimeImmutable $createdAt;

    public bool $ignoredUntilNextOccurrence;

    public bool $ignoredPermanently;

    /** @var array<int, CookieOccurrenceForResolving> */
    public array $occurrences;

    /**
     * @param array<CookieOccurrenceForResolving> $occurrences
     */
    public function __construct(
        string $id,
        string $name,
        string $domain,
        DateTimeImmutable $createdAt,
        bool $ignoredUntilNextOccurrence,
        bool $ignoredPermanently,
        array $occurrences,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->domain = $domain;
        $this->createdAt = $createdAt;
        $this->ignoredUntilNextOccurrence = $ignoredUntilNextOccurrence;
        $this->ignoredPermanently = $ignoredPermanently;
        $this->occurrences = $occurrences;
    }

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
