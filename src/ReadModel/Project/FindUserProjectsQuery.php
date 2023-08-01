<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns array of ProjectView
 */
final class FindUserProjectsQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function create(string $userId): self
    {
        return self::fromParameters([
            'user_id' => $userId,
        ]);
    }

    public function userId(): string
    {
        return $this->getParam('user_id');
    }
}
