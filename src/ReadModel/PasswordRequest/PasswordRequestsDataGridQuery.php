<?php

declare(strict_types=1);

namespace App\ReadModel\PasswordRequest;

use App\ReadModel\AbstractDataGridQuery;

final class PasswordRequestsDataGridQuery extends AbstractDataGridQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
