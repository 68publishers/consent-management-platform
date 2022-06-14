<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CheckCookieProviderExistsInterface
{
	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 *
	 * @return void
	 * @throws \App\Domain\CookieProvider\Exception\CookieProviderNotFoundException
	 */
	public function __invoke(CookieProviderId $cookieProviderId): void;
}
