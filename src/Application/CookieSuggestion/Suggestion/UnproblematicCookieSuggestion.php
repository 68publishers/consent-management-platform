<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

final class UnproblematicCookieSuggestion extends AbstractSuggestion
{
    /** @var non-empty-list<ExistingCookie> */
    private array $existingCookies;

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
    ) {
        parent::__construct($suggestionId, false, $suggestionName, $suggestionDomain, $occurrences);

        $this->existingCookies = $existingCookies;
    }

    /**
     * @return non-empty-list<ExistingCookie>
     */
    public function getExistingCookies(): array
    {
        return $this->existingCookies;
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
