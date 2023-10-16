<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `ConsentStatisticsView`
 */
final class CalculateConsentStatisticsPerPeriodQuery extends AbstractQuery
{
    public static function create(
        string $projectId,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        ?string $environment = null,
    ): self {
        return self::fromParameters([
            'project_id' => $projectId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'environment' => $environment,
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

    public function environment(): ?string
    {
        return $this->getParam('environment');
    }
}
