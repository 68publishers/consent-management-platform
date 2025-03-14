<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `array<ProjectSelectOptionsView>`
 */
final class FindProjectSelectOptionsQuery extends AbstractQuery
{
    public static function all(): self
    {
        return self::fromParameters([]);
    }

    public static function byUser(string $userId): self
    {
        return self::fromParameters([
            'user_id' => $userId,
        ]);
    }

    public static function byCookieProviderId(string $cookieProviderId): self
    {
        return self::fromParameters([
            'cookie_provider_id' => $cookieProviderId,
        ]);
    }

    public function withActiveOnly(bool $activeOnly): self
    {
        return $this->withParam('active_only', $activeOnly);
    }

    public function userId(): ?string
    {
        return $this->getParam('user_id');
    }

    public function cookieProviderId(): ?string
    {
        return $this->getParam('cookie_provider_id');
    }

    public function activeOnly(): bool
    {
        return $this->getParam('active_only') ?? false;
    }
}
