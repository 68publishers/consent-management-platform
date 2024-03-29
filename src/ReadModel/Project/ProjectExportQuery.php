<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractBatchedQuery;

final class ProjectExportQuery extends AbstractBatchedQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
