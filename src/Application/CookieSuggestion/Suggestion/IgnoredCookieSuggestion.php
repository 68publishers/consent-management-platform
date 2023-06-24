<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

final class IgnoredCookieSuggestion implements SuggestionInterface
{
	private SuggestionInterface $originalSuggestion;

	public function __construct(SuggestionInterface $originalSuggestion)
	{
		$this->originalSuggestion = $originalSuggestion;
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
}
