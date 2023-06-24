<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Event;

use Exception;
use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\CookieSuggestion\ValueObject\CookieOccurrenceId;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieOccurrenceLastFoundAtChanged extends AbstractDomainEvent
{
	private CookieSuggestionId $cookieSuggestionId;

	private CookieOccurrenceId $cookieOccurrenceId;

	private DateTimeImmutable $lastFoundAt;

	public static function create(
		CookieSuggestionId $cookieSuggestionId,
		CookieOccurrenceId $cookieOccurrenceId,
		DateTimeImmutable $lastFoundAt
	): self {
		$event = self::occur($cookieSuggestionId->toString(), [
			'cookie_occurrence_id' => $cookieOccurrenceId->toString(),
			'last_found_at' => $lastFoundAt->format(DateTimeInterface::ATOM),
		]);

		$event->cookieSuggestionId = $cookieSuggestionId;
		$event->cookieOccurrenceId = $cookieOccurrenceId;
		$event->lastFoundAt = $lastFoundAt;

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

	public function lastFoundAt(): DateTimeImmutable
	{
		return $this->lastFoundAt;
	}

	/**
	 * @throws Exception
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieSuggestionId = CookieSuggestionId::fromUuid($this->aggregateId()->id());
		$this->cookieOccurrenceId = CookieOccurrenceId::fromString($parameters['cookie_occurrence_id']);
		$this->lastFoundAt = new DateTimeImmutable($parameters['last_found_at']);
	}
}
