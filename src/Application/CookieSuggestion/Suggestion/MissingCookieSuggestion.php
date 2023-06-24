<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Solution\Solutions;
use App\Application\CookieSuggestion\Warning\WarningInterface;

final class MissingCookieSuggestion extends AbstractSuggestion
{
	public Solutions $solutions;

	/**
	 * @param non-empty-list<CookieOccurrence> $occurrences
	 * @param array<int, WarningInterface>     $warnings
	 */
	public function __construct(
		string $suggestionId,
		string $suggestionName,
		string $suggestionDomain,
		array $occurrences,
		array $warnings,
		Solutions $solutions
	) {
		parent::__construct($suggestionId, $suggestionName, $suggestionDomain, $occurrences, $warnings);

		$this->solutions = $solutions;
	}

	public function getSolutions(): Solutions
	{
		return $this->solutions;
	}
}
