<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use App\ReadModel\AbstractDataGridQuery;

final class ConsentsDataGridQuery extends AbstractDataGridQuery
{
    public const int CountLimit = 100_000;

    public static function create(
        string $projectId,
        bool $countEstimateOnly,
    ): self {
        return self::fromParameters([
            'project_id' => $projectId,
            'count_estimate_only' => $countEstimateOnly,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function isCountEstimateOnly(): bool
    {
        return $this->getParam('count_estimate_only');
    }
}
