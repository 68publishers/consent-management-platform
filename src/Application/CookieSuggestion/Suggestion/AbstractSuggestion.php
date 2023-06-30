<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

abstract class AbstractSuggestion implements SuggestionInterface
{
	private string $suggestionId;

	private bool $virtual;

	private string $suggestionName;

	private string $suggestionDomain;

	/** @var non-empty-list<CookieOccurrence> */
	private array $occurrences;

	/**
	 * @param non-empty-list<CookieOccurrence> $occurrences
	 */
	public function __construct(
		string $suggestionId,
		bool $virtual,
		string $suggestionName,
		string $suggestionDomain,
		array $occurrences
	) {
		$this->suggestionId = $suggestionId;
		$this->virtual = $virtual;
		$this->suggestionName = $suggestionName;
		$this->suggestionDomain = $suggestionDomain;
		$this->occurrences = $occurrences;
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

	public function hasWarnings(): bool
	{
		return FALSE;
	}

	public function isVirtual(): bool
	{
		return $this->virtual;
	}

	public function getLatestOccurrence(): ?CookieOccurrence
	{
		$occurrences = $this->occurrences;

		if (0 >= count($occurrences)) {
			return NULL;
		}

		usort($occurrences, static fn (CookieOccurrence $left, CookieOccurrence $right) => $right->lastFoundAt <=> $left->lastFoundAt);

		return array_shift($occurrences);
	}
}
