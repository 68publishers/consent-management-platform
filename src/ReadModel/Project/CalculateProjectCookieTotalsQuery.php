<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `ProjectCookieTotalsView`
 */
final class CalculateProjectCookieTotalsQuery extends AbstractQuery
{
    public static function create(string $projectId, DateTimeImmutable $maxDate): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'max_date' => $maxDate,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function maxDate(): DateTimeImmutable
    {
        return $this->getParam('max_date');
    }
}
