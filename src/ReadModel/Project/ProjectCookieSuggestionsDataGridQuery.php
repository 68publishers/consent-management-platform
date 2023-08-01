<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns `array<ProjectCookieSuggestionsListingItem>`
 */
final class ProjectCookieSuggestionsDataGridQuery extends AbstractDataGridQuery
{
    public static function create(): self
    {
        return self::fromParameters([]);
    }
}
