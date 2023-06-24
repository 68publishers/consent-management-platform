<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Event;

use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieIgnoredUntilNextOccurrenceChanged extends AbstractDomainEvent
{
	private CookieSuggestionId $cookieSuggestionId;

	private bool $ignoredUntilNextOccurrence;

	public static function create(
		CookieSuggestionId $cookieSuggestionId,
		bool $ignoredUntilNextOccurrence
	): self {
		$event = self::occur($cookieSuggestionId->toString(), [
			'ignored_until_next_occurrence' => $ignoredUntilNextOccurrence,
		]);

		$event->cookieSuggestionId = $cookieSuggestionId;
		$event->ignoredUntilNextOccurrence = $ignoredUntilNextOccurrence;

		return $event;
	}

	public function cookieSuggestionId(): CookieSuggestionId
	{
		return $this->cookieSuggestionId;
	}

	public function ignoredUntilNextOccurrence(): bool
	{
		return $this->ignoredUntilNextOccurrence;
	}

	protected function reconstituteState(array $parameters): void
	{
		$this->cookieSuggestionId = CookieSuggestionId::fromUuid($this->aggregateId()->id());
		$this->ignoredUntilNextOccurrence = (bool) $parameters['ignored_until_next_occurrence'];
	}
}
