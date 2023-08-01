<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class SolutionGroup
{
    /** @var non-empty-list<SolutionInterface> */
    private array $solutions;

    /**
     * @param non-empty-list<SolutionInterface> $solutions
     */
    public function __construct(
        private readonly string $name,
        SolutionInterface ...$solutions,
    ) {
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
