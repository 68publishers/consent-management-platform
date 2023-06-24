<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Problem\ProblemInterface;
use App\Application\CookieSuggestion\Warning\WarningInterface;

final class ProblematicCookieSuggestion extends AbstractSuggestion
{
	private ExistingCookie $existingCookie;

	/** @var array<ProblemInterface> */
	public array $problems;

	/**
	 * @param non-empty-list<CookieOccurrence> $occurrences
	 * @param array<int, WarningInterface>     $warnings
	 * @param non-empty-list<ProblemInterface> $problems
	 */
	public function __construct(
		string $suggestionId,
		string $suggestionName,
		string $suggestionDomain,
		array $occurrences,
		array $warnings,
		ExistingCookie $existingCookie,
		array $problems
	) {
		parent::__construct($suggestionId, $suggestionName, $suggestionDomain, $occurrences, $warnings);

		$this->existingCookie = $existingCookie;
		$this->problems = $problems;
	}

	public function getExistingCookie(): ExistingCookie
	{
		return $this->existingCookie;
	}

	/**
	 * @return non-empty-list<ProblemInterface>
	 */
	public function getProblems(): array
	{
		return $this->problems;
	}
}
