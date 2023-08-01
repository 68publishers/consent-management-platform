<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns CookieView or NULL
 */
final class GetCookieByIdQuery extends AbstractQuery
{
    /**
     * @return static
     */
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
