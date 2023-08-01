<?php

declare(strict_types=1);

namespace App\ReadModel\Category;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?CategoryView`
 */
final class GetCategoryByIdQuery extends AbstractQuery
{
    public static function create(string $id): self
    {
        return self::fromParameters([
            'id' => $id,
        ]);
    }

    public function id(): string
    {
        return $this->getParam('id');
    }
}
