<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Solution\Solutions;

final class MissingCookieSuggestion extends AbstractSuggestion
{
	public Solutions $solutions;

	/**
	 * @param non-empty-list<CookieOccurrence> $occurrences
	 */
	public function __construct(
		string $suggestionId,
		string $suggestionName,
		string $suggestionDomain,
		array $occurrences,
		Solutions $solutions
	) {
		parent::__construct($suggestionId, FALSE, $suggestionName, $suggestionDomain, $occurrences);

		$this->solutions = $solutions;
	}

	public function getSolutions(): Solutions
	{
		return $this->solutions;
	}
}
