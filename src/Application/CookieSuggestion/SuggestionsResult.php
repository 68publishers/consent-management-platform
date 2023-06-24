<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion;

use App\Application\CookieSuggestion\Suggestion\SuggestionInterface;

final class SuggestionsResult
{
	/** @var array<SuggestionInterface> */
	private array $suggestions = [];

	public function withSuggestion(SuggestionInterface $suggestion): self
	{
		$result = clone $this;
		$result->suggestions[] = $suggestion;

		return $result;
	}

	/**
	 * @template T of SuggestionInterface
	 *
	 * @param class-string<T> $classname
	 *
	 * @return array<T>
	 */
	public function getSuggestions(string $classname): array
	{
		return array_values(
			array_filter(
				$this->suggestions,
				static fn (SuggestionInterface $suggestion): bool => is_a($suggestion, $classname)
			)
		);
	}
}
