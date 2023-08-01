<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

final class ProjectCookieSuggestionsListingItem
{
    public function __construct(
        public string $id,
        public string $code,
        public string $name,
        public ProjectCookieSuggestionsStatistics $statistics,
    ) {}
}
