<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?CookieProviderView`
 */
final class GetCookieProviderByCodeQuery extends AbstractQuery
{
    public static function create(string $code): self
    {
        return self::fromParameters([
            'code' => $code,
        ]);
    }

    public function code(): string
    {
        return $this->getParam('code');
    }
}
