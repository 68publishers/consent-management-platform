<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider;

use App\Domain\CookieProvider\Exception\CookieProviderNotFoundException;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CookieProviderRepositoryInterface
{
    public function save(CookieProvider $cookieProvider): void;

    /**
     * @throws CookieProviderNotFoundException
     */
    public function get(CookieProviderId $id): CookieProvider;
}
