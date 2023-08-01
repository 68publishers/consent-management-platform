<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?CookieProviderView`
 */
final class GetCookieProviderByIdQuery extends AbstractQuery
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
