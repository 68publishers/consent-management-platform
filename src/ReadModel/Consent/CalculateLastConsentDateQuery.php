<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?DateTimeImmutable`
 */
final class CalculateLastConsentDateQuery extends AbstractQuery
{
    public static function create(
        string $projectId,
        DateTimeImmutable $maxDate,
        ?string $environment = null,
    ): self {
        return self::fromParameters([
            'project_id' => $projectId,
            'max_date' => $maxDate,
            'environment' => $environment,
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

    public function environment(): ?string
    {
        return $this->getParam('environment');
    }
}
