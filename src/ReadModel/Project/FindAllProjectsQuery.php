<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns array of ProjectView
 */
final class FindAllProjectsQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
