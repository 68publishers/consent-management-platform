<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\Cookie\ValueObject\CookieId;

interface CookieRepositoryInterface
{
	/**
	 * @param \App\Domain\Cookie\Cookie $cookie
	 *
	 * @return void
	 */
	public function save(Cookie $cookie): void;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId $id
	 *
	 * @return \App\Domain\Cookie\Cookie
	 * @throws \App\Domain\Cookie\Exception\CookieNotFoundException
	 */
	public function get(CookieId $id): Cookie;
}
