<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion;

use App\Domain\CookieSuggestion\ValueObject\AcceptedCategories;
use App\Domain\CookieSuggestion\ValueObject\CookieOccurrenceId;
use App\Domain\CookieSuggestion\ValueObject\FoundOnUrl;
use App\Domain\CookieSuggestion\ValueObject\ScenarioName;
use DateTimeImmutable;

final class CookieOccurrence
{
    public function __construct(
        private readonly CookieSuggestion $cookieSuggestion,
        private readonly CookieOccurrenceId $id,
        private readonly ScenarioName $scenarioName,
        private FoundOnUrl $foundOnUrl,
        private AcceptedCategories $acceptedCategories,
        private DateTimeImmutable $lastFoundAt,
    ) {}

    public function cookieSuggestion(): CookieSuggestion
    {
        return $this->cookieSuggestion;
    }

    public function id(): CookieOccurrenceId
    {
        return $this->id;
    }

    public function scenarioName(): ScenarioName
    {
        return $this->scenarioName;
    }

    public function foundOnUrl(): FoundOnUrl
    {
        return $this->foundOnUrl;
    }

    public function acceptedCategories(): AcceptedCategories
    {
        return $this->acceptedCategories;
    }

    public function lastFoundAt(): DateTimeImmutable
    {
        return $this->lastFoundAt;
    }

    public function setFoundOnUrl(FoundOnUrl $foundOnUrl): void
    {
        $this->foundOnUrl = $foundOnUrl;
    }

    public function setAcceptedCategories(AcceptedCategories $acceptedCategories): void
    {
        $this->acceptedCategories = $acceptedCategories;
    }

    public function setLastFoundAt(DateTimeImmutable $lastFoundAt): void
    {
        $this->lastFoundAt = $lastFoundAt;
    }
}
