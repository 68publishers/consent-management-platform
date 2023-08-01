<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ConsentStatisticsView
 */
final class CalculateConsentStatisticsPerPeriodQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function create(string $projectId, DateTimeInterface $startDate, DateTimeInterface $endDate): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function startDate(): DateTimeInterface
    {
        return $this->getParam('start_date');
    }

    public function endDate(): DateTimeInterface
    {
        return $this->getParam('end_date');
    }
}
