<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use DateTimeImmutable;

final class CookieSuggestion
{
    public string $id;

    public string $projectId;

    public string $name;

    public string $domain;

    public DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $projectId,
        string $name,
        string $domain,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->name = $name;
        $this->domain = $domain;
        $this->createdAt = $createdAt;
    }
}
