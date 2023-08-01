<?php

declare(strict_types=1);

namespace App\ReadModel\Import;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns `array<ImportListView>`
 */
final class ImportDataGridQuery extends AbstractDataGridQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
