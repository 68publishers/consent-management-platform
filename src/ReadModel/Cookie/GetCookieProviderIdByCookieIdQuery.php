<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?string`
 */
final class GetCookieProviderIdByCookieIdQuery extends AbstractQuery
{
    public static function create(string $cookieId): self
    {
        return self::fromParameters([
            'cookie_id' => $cookieId,
        ]);
    }

    public function cookieId(): string
    {
        return $this->getParam('cookie_id');
    }
}
