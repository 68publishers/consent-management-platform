<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CookieProviderRepositoryInterface
{
	/**
	 * @param \App\Domain\CookieProvider\CookieProvider $cookieProvider
	 *
	 * @return void
	 */
	public function save(CookieProvider $cookieProvider): void;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $id
	 *
	 * @return \App\Domain\CookieProvider\CookieProvider
	 * @throws \App\Domain\CookieProvider\Exception\CookieProviderNotFoundException
	 */
	public function get(CookieProviderId $id): CookieProvider;
}
