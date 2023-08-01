<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use DateTimeImmutable;

final class CookieSuggestion
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $name,
        public string $domain,
        public DateTimeImmutable $createdAt,
    ) {}
}
