<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `MonthlyStatistics`
 */
final class CountFullyPositiveConsentsPerMonthQuery extends AbstractQuery
{
    public static function create(
        string $projectId,
        int $year,
        bool $unique,
    ): self {
        return self::fromParameters([
            'project_id' => $projectId,
            'year' => $year,
            'unique' => $unique,
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
}
