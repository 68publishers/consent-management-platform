<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Solution\Solutions;

final class IgnoredCookieSuggestion implements SuggestionInterface
{
	private SuggestionInterface $originalSuggestion;

	private bool $permanentlyIgnored;

	private Solutions $solutions;

	public function __construct(SuggestionInterface $originalSuggestion, bool $permanentlyIgnored, Solutions $solutions)
	{
		$this->originalSuggestion = $originalSuggestion;
		$this->permanentlyIgnored = $permanentlyIgnored;
		$this->solutions = $solutions;
	}

	public function getSuggestionId(): string
	{
		return $this->originalSuggestion->getSuggestionId();
	}

	public function getSuggestionName(): string
	{
		return $this->originalSuggestion->getSuggestionName();
	}

	public function getSuggestionDomain(): string
	{
		return $this->originalSuggestion->getSuggestionDomain();
	}

	public function getOccurrences(): array
	{
		return $this->originalSuggestion->getOccurrences();
	}

	public function getWarnings(): array
	{
		return $this->originalSuggestion->getWarnings();
	}

	public function getLatestOccurrence(): CookieOccurrence
	{
		return $this->originalSuggestion->getLatestOccurrence();
	}

	public function getOriginalSuggestion(): SuggestionInterface
	{
		return $this->originalSuggestion;
	}

	public function isPermanentlyIgnored(): bool
	{
		return $this->permanentlyIgnored;
	}

	public function getSolutions(): Solutions
	{
		return $this->solutions;
	}
}
