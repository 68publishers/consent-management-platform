<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Warning\WarningInterface;

interface SuggestionInterface
{
	public function getSuggestionId(): string;

	public function getSuggestionName(): string;

	public function getSuggestionDomain(): string;

	/**
	 * @return non-empty-list<CookieOccurrence>
	 */
	public function getOccurrences(): array;

	/**
	 * @return array<WarningInterface>
	 */
	public function getWarnings(): array;

	public function getLatestOccurrence(): CookieOccurrence;
}
