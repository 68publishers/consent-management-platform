<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class IgnorePermanently implements SolutionInterface
{
	public function getType(): string
	{
		return 'ignore_permanently';
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
