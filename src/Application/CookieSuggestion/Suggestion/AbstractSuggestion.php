<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Warning\WarningInterface;

abstract class AbstractSuggestion implements SuggestionInterface
{
	private string $suggestionId;

	private string $suggestionName;

	private string $suggestionDomain;

	/** @var non-empty-list<CookieOccurrence> */
	private array $occurrences;

	/** @var array<int, WarningInterface> */
	private array $warnings;

	/**
	 * @param non-empty-list<CookieOccurrence> $occurrences
	 * @param array<int, WarningInterface>     $warnings
	 */
	public function __construct(
		string $suggestionId,
		string $suggestionName,
		string $suggestionDomain,
		array $occurrences,
		array $warnings
	) {
		$this->suggestionId = $suggestionId;
		$this->suggestionName = $suggestionName;
		$this->suggestionDomain = $suggestionDomain;
		$this->occurrences = $occurrences;
		$this->warnings = $warnings;
	}

	public function getSuggestionId(): string
	{
		return $this->suggestionId;
	}

	public function getSuggestionName(): string
	{
		return $this->suggestionName;
	}

	public function getSuggestionDomain(): string
	{
		return $this->suggestionDomain;
	}

	public function getOccurrences(): array
	{
		return $this->occurrences;
	}

	public function getWarnings(): array
	{
		return $this->warnings;
	}

	public function getLatestOccurrence(): CookieOccurrence
	{
		$occurrences = $this->occurrences;

		usort($occurrences, static fn (CookieOccurrence $left, CookieOccurrence $right) => $right->lastFoundAt <=> $left->lastFoundAt);

		return array_shift($occurrences);
	}
}
