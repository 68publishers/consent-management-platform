<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class GetConsentByIdAndProjectIdQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function create(string $id, string $projectId): self
    {
        return self::fromParameters([
            'id' => $id,
            'project_id' => $projectId,
        ]);
    }

    public function id(): string
    {
        return $this->getParam('id');
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }
}
