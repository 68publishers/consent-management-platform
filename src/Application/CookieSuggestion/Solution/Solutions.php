<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

use App\Application\CookieSuggestion\DataStore\DataStoreInterface;
use InvalidArgumentException;

final class Solutions
{
    private string $uniqueKey;

    /** @var non-empty-list<SolutionInterface|SolutionGroup> */
    private array $solutions;

    /**
     * @param non-empty-list<string>          $compositeKey
     * @param SolutionInterface|SolutionGroup ...$solutions
     */
    public function __construct(
        private readonly string $projectId,
        private readonly string $cookieSuggestionId,
        array $compositeKey,
        private readonly DataStoreInterface $dataStore,
        ...$solutions,
    ) {
        $this->solutions = $solutions;
        $this->uniqueKey = md5(
            implode(
                '__',
                array_merge(
                    [$projectId, $cookieSuggestionId],
                    $compositeKey,
                ),
            ),
        );
    }

    /**
     * @return non-empty-list<SolutionInterface|SolutionGroup>
     */
    public function all(): array
    {
        return $this->solutions;
    }

    public function reset(): void
    {
        $this->dataStore->remove($this->projectId, $this->uniqueKey);
    }

    /**
     * @return array<string, mixed>
     */
    public function getSolutionArguments(SolutionInterface $solution): array
    {
        $found = false;

        foreach ($this->all() as $group) {
            $solutions = $group instanceof SolutionGroup ? $group->getSolutions() : [$group];

            foreach ($solutions as $s) {
                if ($s === $solution) {
                    $found = true;

                    break 2;
                }
            }
        }

        if (!$found) {
            throw new InvalidArgumentException(sprintf(
                'Passed Solution "%s" is not managed by current Solutions instance.',
                $solution->getType(),
            ));
        }

        return [
            'solutionsUniqueId' => $this->uniqueKey,
            'solutionUniqueId' => $solution->getUniqueId(),
            'solutionType' => $solution->getType(),
            'cookieSuggestionId' => $this->cookieSuggestionId,
            'args' => $solution->getArguments(),
        ];
    }

    /**
     * @return array{
     *     solutions_unique_id: string,
     *     solution_unique_id: string,
     *     solution_type: string,
     *     values: array<string, mixed>
     * }|null
     */
    public function getDataForResolving(): ?array
    {
        $data = $this->dataStore->get($this->projectId, $this->uniqueKey);

        if (!is_array($data)) {
            return null;
        }

        $solutionUniqueId = $data['solutionUniqueId'] ?? '';
        $solutionType = $data['solutionType'] ?? '';

        foreach ($this->solutions as $group) {
            $solutions = $group instanceof SolutionGroup ? $group->getSolutions() : [$group];

            foreach ($solutions as $solution) {
                if ($solutionType === $solution->getType() && $solutionUniqueId === $solution->getUniqueId()) {
                    return [
                        'solutionsUniqueId' => $this->uniqueKey,
                        'solutionUniqueId' => $solutionUniqueId,
                        'solutionType' => $solutionType,
                        'cookieSuggestionId' => $data['cookieSuggestionId'],
                        'values' => $data['values'] ?? [],
                    ];
                }
            }
        }

        return null;
    }
}
