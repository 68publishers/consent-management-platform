<?php

declare(strict_types=1);

namespace App\ReadModel\GlobalSettings;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?GlobalSettingsView`
 */
final class GetGlobalSettingsQuery extends AbstractQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
