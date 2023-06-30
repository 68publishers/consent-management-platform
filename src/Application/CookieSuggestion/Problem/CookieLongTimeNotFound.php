<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Problem;

use App\Application\CookieSuggestion\Solution\Solutions;

final class CookieLongTimeNotFound implements ProblemInterface
{
	public const TYPE = 'cookie_long_time_not_found';

	private int $notFoundForDays;

	private Solutions $solutions;

	public function __construct(
		int $notFoundForDays,
		Solutions $solutions
	) {
		$this->notFoundForDays = $notFoundForDays;
		$this->solutions = $solutions;
	}

	public function getType(): string
	{
		return self::TYPE;
	}

	public function getTranslatorArgs(): array
	{
		return [
			'not_found_for_days' => $this->notFoundForDays,
		];
	}

	public function getSolutions(): Solutions
	{
		return $this->solutions;
	}
}
