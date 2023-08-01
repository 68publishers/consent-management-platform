<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Event;

use App\Domain\CookieSuggestion\ValueObject\CookieOccurrenceId;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use App\Domain\CookieSuggestion\ValueObject\FoundOnUrl;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieOccurrenceFoundOnUrlChanged extends AbstractDomainEvent
{
    private CookieSuggestionId $cookieSuggestionId;

    private CookieOccurrenceId $cookieOccurrenceId;

    private FoundOnUrl $foundOnUrl;

    public static function create(
        CookieSuggestionId $cookieSuggestionId,
        CookieOccurrenceId $cookieOccurrenceId,
        FoundOnUrl $foundOnUrl,
    ): self {
        $event = self::occur($cookieSuggestionId->toString(), [
            'cookie_occurrence_id' => $cookieOccurrenceId->toString(),
            'found_on_url' => $foundOnUrl->value(),
        ]);

        $event->cookieSuggestionId = $cookieSuggestionId;
        $event->cookieOccurrenceId = $cookieOccurrenceId;
        $event->foundOnUrl = $foundOnUrl;

        return $event;
    }

    public function cookieSuggestionId(): CookieSuggestionId
    {
        return $this->cookieSuggestionId;
    }

    public function cookieOccurrenceId(): CookieOccurrenceId
    {
        return $this->cookieOccurrenceId;
    }

    public function foundOnUrl(): FoundOnUrl
    {
        return $this->foundOnUrl;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieSuggestionId = CookieSuggestionId::fromUuid($this->aggregateId()->id());
        $this->cookieOccurrenceId = CookieOccurrenceId::fromString($parameters['cookie_occurrence_id']);
        $this->foundOnUrl = FoundOnUrl::fromValue($parameters['found_on_url']);
    }
}
