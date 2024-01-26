<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use DateTimeImmutable;

final class ExternalAuthView
{
    public function __construct(
        public readonly string $userId,
        public readonly string $providerCode,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly string $resourceOwnerId,
    ) {}
}
