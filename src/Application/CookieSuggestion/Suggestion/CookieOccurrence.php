<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use DateTimeImmutable;
use App\ReadModel\CookieSuggestion\CookieOccurrenceForResolving;

final class CookieOccurrence
{
	public string $id;

	public string $scenarioName;

	public string $foundOnUrl;

	/** @var array<int, string> */
	public array $acceptedCategories;

	public DateTimeImmutable $lastFoundAt;

	/**
	 * @param array<int, string> $acceptedCategories
	 */
	public function __construct(
		string $id,
		string $scenarioName,
		string $foundOnUrl,
		array $acceptedCategories,
		DateTimeImmutable $lastFoundAt
	) {
		$this->id = $id;
		$this->scenarioName = $scenarioName;
		$this->foundOnUrl = $foundOnUrl;
		$this->acceptedCategories = $acceptedCategories;
		$this->lastFoundAt = $lastFoundAt;
	}

	public static function fromCookieOccurrenceForResolving(CookieOccurrenceForResolving $cookieOccurrenceForResolving): self
	{
		return new self(
			$cookieOccurrenceForResolving->id,
			$cookieOccurrenceForResolving->scenarioName,
			$cookieOccurrenceForResolving->foundOnUrl,
			$cookieOccurrenceForResolving->acceptedCategories,
			$cookieOccurrenceForResolving->lastFoundAt,
		);
	}
}
