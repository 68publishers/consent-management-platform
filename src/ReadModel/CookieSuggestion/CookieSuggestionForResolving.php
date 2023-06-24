<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

final class CookieSuggestionForResolving
{
	public string $id;

	public string $name;

	public string $domain;

	public bool $ignored;

	/** @var array<int, CookieOccurrenceForResolving> */
	public array $occurrences;

	/**
	 * @param array<CookieOccurrenceForResolving> $occurrences
	 */
	public function __construct(
		string $id,
		string $name,
		string $domain,
		bool $ignored,
		array $occurrences
	) {
		$this->id = $id;
		$this->name = $name;
		$this->domain = $domain;
		$this->ignored = $ignored;
		$this->occurrences = $occurrences;
	}
}
