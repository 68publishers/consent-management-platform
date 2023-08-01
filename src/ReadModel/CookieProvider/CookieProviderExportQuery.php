<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractBatchedQuery;

final class CookieProviderExportQuery extends AbstractBatchedQuery
{
    /**
     * @return static
     */
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
