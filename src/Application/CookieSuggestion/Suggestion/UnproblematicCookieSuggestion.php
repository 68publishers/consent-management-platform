<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Warning\WarningInterface;

final class UnproblematicCookieSuggestion extends AbstractSuggestion
{
	private ExistingCookie $existingCookie;

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
		ExistingCookie $existingCookie
	) {
		parent::__construct($suggestionId, $suggestionName, $suggestionDomain, $occurrences, $warnings);

		$this->existingCookie = $existingCookie;
	}

	public function getExistingCookie(): ExistingCookie
	{
		return $this->existingCookie;
	}
}
