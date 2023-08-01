<?php

declare(strict_types=1);

namespace App\ReadModel\ConsentSettings;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns integer
 */
final class GetLatestShortIdentifierQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function create(string $projectId): self
    {
        return self::fromParameters([
            'projectId' => $projectId,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('projectId');
    }
}
