<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ProjectId instance or FALSE
 */
final class ProjectExistsQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function byId(string $projectId): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
        ]);
    }

    /**
     * @return static
     */
    public static function byCode(string $code): self
    {
        return self::fromParameters([
            'code' => $code,
        ]);
    }

    public function projectId(): ?string
    {
        return $this->getParam('project_id');
    }

    public function code(): ?string
    {
        return $this->getParam('code');
    }
}
