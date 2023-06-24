<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Event;

use Exception;
use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\CookieSuggestion\ValueObject\FoundOnUrl;
use App\Domain\CookieSuggestion\ValueObject\ScenarioName;
use App\Domain\CookieSuggestion\ValueObject\AcceptedCategories;
use App\Domain\CookieSuggestion\ValueObject\CookieOccurrenceId;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieOccurrenceAdded extends AbstractDomainEvent
{
	private CookieSuggestionId $cookieSuggestionId;

	private CookieOccurrenceId $cookieOccurrenceId;

	private ScenarioName $scenarioName;

	private FoundOnUrl $foundOnUrl;

	private AcceptedCategories $acceptedCategories;

	private DateTimeImmutable $lastFoundAt;

	public static function create(
		CookieSuggestionId $cookieSuggestionId,
		CookieOccurrenceId $cookieOccurrenceId,
		ScenarioName $scenarioName,
		FoundOnUrl $foundOnUrl,
		AcceptedCategories $acceptedCategories,
		DateTimeImmutable $lastFoundAt
	): self {
		$event = self::occur($cookieSuggestionId->toString(), [
			'cookie_occurrence_id' => $cookieOccurrenceId->toString(),
			'scenario_name' => $scenarioName->value(),
			'found_on_url' => $foundOnUrl->value(),
			'accepted_categories' => $acceptedCategories->toArray(),
			'last_found_at' => $lastFoundAt->format(DateTimeInterface::ATOM),
		]);

		$event->cookieSuggestionId = $cookieSuggestionId;
		$event->cookieOccurrenceId = $cookieOccurrenceId;
		$event->scenarioName = $scenarioName;
		$event->foundOnUrl = $foundOnUrl;
		$event->acceptedCategories = $acceptedCategories;
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

	/**
	 * @throws Exception
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->cookieSuggestionId = CookieSuggestionId::fromUuid($this->aggregateId()->id());
		$this->cookieOccurrenceId = CookieOccurrenceId::fromString($parameters['cookie_occurrence_id']);
		$this->scenarioName = ScenarioName::fromValue($parameters['scenario_name']);
		$this->foundOnUrl = FoundOnUrl::fromValue($parameters['found_on_url']);
		$this->acceptedCategories = AcceptedCategories::reconstitute($parameters['accepted_categories']);
		$this->lastFoundAt = new DateTimeImmutable($parameters['last_found_at']);
	}
}
