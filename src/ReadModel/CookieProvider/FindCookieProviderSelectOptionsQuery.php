<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `array<CookieProviderSelectOptionView>`
 */
final class FindCookieProviderSelectOptionsQuery extends AbstractQuery
{
    public static function all(): self
    {
        return self::fromParameters([]);
    }

    public static function assignedToProject(string $projectId): self
    {
        return self::fromParameters([
            'assigned_project_id' => $projectId,
        ]);
    }

    public function withPrivate(bool|string $booleanOrProjectId): self
    {
        return $this->withParam('private', $booleanOrProjectId);
    }

    public function assignedProjectId(): ?string
    {
        return $this->getParam('assigned_project_id');
    }

    public function private(): bool|string
    {
        return $this->getParam('private') ?? false;
    }
}
