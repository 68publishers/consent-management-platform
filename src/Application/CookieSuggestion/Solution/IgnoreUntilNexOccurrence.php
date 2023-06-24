<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class IgnoreUntilNexOccurrence implements SolutionInterface
{
	public function getType(): string
	{
		return 'ignore_until_next_occurrence';
	}

	public function getUniqueId(): string
	{
		return md5($this->getType());
	}

	public function getArguments(): array
	{
		return [];
	}
}
