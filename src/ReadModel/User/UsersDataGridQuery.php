<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\ReadModel\AbstractDataGridQuery;

final class UsersDataGridQuery extends AbstractDataGridQuery
{
    /**
     * @return $this
     */
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
