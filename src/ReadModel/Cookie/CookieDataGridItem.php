<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use DateTimeImmutable;

final class CookieDataGridItem
{
    /**
     * @param array<int, CookieProjectItem> $projects
     * @param bool|array<int, string|null>  $environments
     */
    public function __construct(
        public readonly string $id,
        public readonly string $cookieName,
        public readonly string $processingTime,
        public readonly bool $active,
        public readonly ?string $categoryId,
        public readonly ?string $categoryName,
        public readonly string $cookieProviderId,
        public readonly string $cookieProviderName,
        public readonly string $cookieProviderType,
        public readonly bool $cookieProviderPrivate,
        public readonly DateTimeImmutable $createdAt,
        public readonly array $projects,
        public bool|array $environments,
    ) {}
}
