<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `array<ProjectView>`
 */
final class FindAllProjectsQuery extends AbstractQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
