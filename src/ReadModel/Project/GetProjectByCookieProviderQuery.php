<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?ProjectView`
 */
final class GetProjectByCookieProviderQuery extends AbstractQuery
{
    public static function create(string $cookieProviderId): self
    {
        return self::fromParameters([
            'cookie_provider_id' => $cookieProviderId,
        ]);
    }

    public function cookieProviderId(): string
    {
        return $this->getParam('cookie_provider_id');
    }
}
