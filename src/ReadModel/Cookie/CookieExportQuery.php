<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractBatchedQuery;

final class CookieExportQuery extends AbstractBatchedQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
