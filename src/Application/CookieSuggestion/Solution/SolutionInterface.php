<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

interface SolutionInterface
{
	public function getType(): string;

	public function getUniqueId(): string;

	/**
	 * @return array<string, mixed>
	 */
	public function getArguments(): array;

	/**
	 * @return array<string, string|numeric>
	 */
	public function getTranslatorArgs(): array;
}
