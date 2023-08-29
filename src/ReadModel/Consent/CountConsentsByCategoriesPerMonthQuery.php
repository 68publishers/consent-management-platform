<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `MonthlyStatistics`
 */
final class CountConsentsByCategoriesPerMonthQuery extends AbstractQuery
{
    /**
     * @param array<int, string> $acceptedCategories
     * @param array<int, string> $rejectedCategories
     */
    public static function create(
        string $projectId,
        int $year,
        bool $unique,
        array $acceptedCategories,
        array $rejectedCategories,
    ): self {
        return self::fromParameters([
            'project_id' => $projectId,
            'year' => $year,
            'unique' => $unique,
            'accepted_categories' => $acceptedCategories,
            'rejected_categories' => $rejectedCategories,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function year(): int
    {
        return $this->getParam('year');
    }

    public function unique(): bool
    {
        return $this->getParam('unique');
    }

    /**
     * @return array<int, string>
     */
    public function acceptedCategories(): array
    {
        return $this->getParam('accepted_categories');
    }

    /**
     * @return array<int, string>
     */
    public function rejectedCategories(): array
    {
        return $this->getParam('rejected_categories');
    }
}
