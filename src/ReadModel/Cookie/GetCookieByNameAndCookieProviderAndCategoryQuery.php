<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?CookieView`
 */
final class GetCookieByNameAndCookieProviderAndCategoryQuery extends AbstractQuery
{
    public static function create(string $name, string $cookieProviderId, string $categoryId): self
    {
        return self::fromParameters([
            'name' => $name,
            'cookie_provider_id' => $cookieProviderId,
            'category_id' => $categoryId,
        ]);
    }

    public function name(): string
    {
        return $this->getParam('name');
    }

    public function cookieProviderId(): string
    {
        return $this->getParam('cookie_provider_id');
    }

    public function categoryId(): string
    {
        return $this->getParam('category_id');
    }
}
