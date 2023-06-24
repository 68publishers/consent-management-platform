<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Solution\Solutions;
use App\Application\CookieSuggestion\Warning\WarningInterface;

final class UnassociatedCookieSuggestion extends AbstractSuggestion
{
	private ExistingCookie $existingCookie;

	private Solutions $solutions;

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
		ExistingCookie $existingCookie,
		Solutions $solutions
	) {
		parent::__construct($suggestionId, $suggestionName, $suggestionDomain, $occurrences, $warnings);

		$this->existingCookie = $existingCookie;
		$this->solutions = $solutions;
	}

	public function getExistingCookie(): ExistingCookie
	{
		return $this->existingCookie;
	}

	public function getSolutions(): Solutions
	{
		return $this->solutions;
	}
}
