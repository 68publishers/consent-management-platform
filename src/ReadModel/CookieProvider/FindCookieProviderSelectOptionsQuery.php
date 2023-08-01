<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns CookieProviderSelectOptionView[]
 */
final class FindCookieProviderSelectOptionsQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function all(): self
    {
        return self::fromParameters([]);
    }

    /**
     * @return static
     */
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
