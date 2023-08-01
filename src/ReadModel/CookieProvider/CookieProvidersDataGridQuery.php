<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns `array<CookieProviderView>`
 */
final class CookieProvidersDataGridQuery extends AbstractDataGridQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
