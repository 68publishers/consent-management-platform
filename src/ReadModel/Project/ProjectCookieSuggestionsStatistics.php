<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use DateTimeImmutable;

final class ProjectCookieSuggestionsStatistics
{
    public function __construct(
        public int $missing,
        public int $unassociated,
        public int $problematic,
        public int $unproblematic,
        public int $ignored,
        public int $total,
        public int $totalWithoutVirtual,
        public ?DateTimeImmutable $latestFoundAt,
    ) {}
}
