<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `array<string>`
 */
final class FindAllProjectIdsQuery extends AbstractQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
