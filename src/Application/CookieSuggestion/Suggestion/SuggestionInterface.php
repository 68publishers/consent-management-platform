<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

interface SuggestionInterface
{
    public function getSuggestionId(): string;

    public function getSuggestionName(): string;

    public function getSuggestionDomain(): string;

    /**
     * @return non-empty-list<CookieOccurrence>
     */
    public function getOccurrences(): array;

    public function hasWarnings(): bool;

    public function isVirtual(): bool;

    public function getLatestOccurrence(): ?CookieOccurrence;
}
