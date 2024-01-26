<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class FindExternalAuthenticationsQuery extends AbstractQuery
{
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
