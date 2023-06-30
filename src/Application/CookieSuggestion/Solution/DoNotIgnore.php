<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class DoNotIgnore implements SolutionInterface
{
	public function getType(): string
	{
		return 'do_not_ignore';
	}

	public function getUniqueId(): string
	{
		return md5($this->getType());
	}

	public function getArguments(): array
	{
		return [];
	}

	public function getTranslatorArgs(): array
	{
		return [];
	}
}
