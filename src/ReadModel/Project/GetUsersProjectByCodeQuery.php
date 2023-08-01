<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ProjectView
 */
final class GetUsersProjectByCodeQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function create(string $code, string $userId): self
    {
        return self::fromParameters([
            'code' => $code,
            'user_id' => $userId,
        ]);
    }

    public function code(): string
    {
        return $this->getParam('code');
    }

    public function userId(): string
    {
        return $this->getParam('user_id');
    }
}
