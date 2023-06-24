<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Problem;

use App\Application\CookieSuggestion\Solution\Solutions;
use App\Application\CookieSuggestion\Suggestion\CookieOccurrence;

final class CookieIsInCategoryThatIsNotAcceptedByScenario implements ProblemInterface
{
	public const TYPE = 'cookie_is_in_category_that_is_not_accepted';

	public string $categoryCode;

	public array $acceptedCategories;

	public CookieOccurrence $occurrence;

	private Solutions $solutions;

	/**
	 * @param array<int, string> $acceptedCategories
	 */
	public function __construct(
		string $categoryCode,
		array $acceptedCategories,
		CookieOccurrence $occurrence,
		Solutions $solutions
	) {
		$this->categoryCode = $categoryCode;
		$this->acceptedCategories = $acceptedCategories;
		$this->occurrence = $occurrence;
		$this->solutions = $solutions;
	}

	public function getType(): string
	{
		return self::TYPE;
	}

	public function getTranslatorArgs(): array
	{
		return [
			'categoryCode' => $this->categoryCode,
			'acceptedCategories' => implode(', ', $this->acceptedCategories),
			'scenarioName' => $this->occurrence->scenarioName,
		];
	}

	public function getSolutions(): Solutions
	{
		return $this->solutions;
	}
}
