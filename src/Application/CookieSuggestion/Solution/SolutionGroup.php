<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class SolutionGroup
{
	private string $name;

	/** @var non-empty-list<SolutionInterface> */
	private array $solutions;

	/**
	 * @param non-empty-list<SolutionInterface> $solutions
	 */
	public function __construct(string $name, SolutionInterface ...$solutions)
	{
		$this->name = $name;
		$this->solutions = $solutions;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return non-empty-list<SolutionInterface>
	 */
	public function getSolutions(): array
	{
		return $this->solutions;
	}
}
