<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\CookieProvider\Exception\CookieProviderNotFoundException;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CheckCookieProviderExistsInterface
{
    /**
     * @throws CookieProviderNotFoundException
     */
    public function __invoke(CookieProviderId $cookieProviderId): void;
}
