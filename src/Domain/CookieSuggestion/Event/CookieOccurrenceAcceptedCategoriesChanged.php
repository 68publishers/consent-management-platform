<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Event;

use App\Domain\CookieSuggestion\ValueObject\AcceptedCategories;
use App\Domain\CookieSuggestion\ValueObject\CookieOccurrenceId;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieOccurrenceAcceptedCategoriesChanged extends AbstractDomainEvent
{
    private CookieSuggestionId $cookieSuggestionId;

    private CookieOccurrenceId $cookieOccurrenceId;

    private AcceptedCategories $acceptedCategories;

    public static function create(
        CookieSuggestionId $cookieSuggestionId,
        CookieOccurrenceId $cookieOccurrenceId,
        AcceptedCategories $acceptedCategories,
    ): self {
        $event = self::occur($cookieSuggestionId->toString(), [
            'cookie_occurrence_id' => $cookieOccurrenceId->toString(),
            'accepted_categories' => $acceptedCategories->toArray(),
        ]);

        $event->cookieSuggestionId = $cookieSuggestionId;
        $event->cookieOccurrenceId = $cookieOccurrenceId;
        $event->acceptedCategories = $acceptedCategories;

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

    public function acceptedCategories(): AcceptedCategories
    {
        return $this->acceptedCategories;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieSuggestionId = CookieSuggestionId::fromUuid($this->aggregateId()->id());
        $this->cookieOccurrenceId = CookieOccurrenceId::fromString($parameters['cookie_occurrence_id']);
        $this->acceptedCategories = AcceptedCategories::reconstitute($parameters['accepted_categories']);
    }
}
