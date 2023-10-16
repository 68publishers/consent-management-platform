<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use DateTimeImmutable;

final class ConsentListView
{
    public function __construct(
        public string $id,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $lastUpdateAt,
        public string $userIdentifier,
        public string $environment,
        public ?string $settingsChecksum = null,
        public ?int $settingsShortIdentifier = null,
        public ?string $settingsId = null,
    ) {}
}
