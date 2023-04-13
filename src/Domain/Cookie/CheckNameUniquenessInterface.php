<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CheckNameUniquenessInterface
{
	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId                 $cookieId
	 * @param \App\Domain\Cookie\ValueObject\Name                     $name
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\Category\ValueObject\CategoryId             $categoryId
	 *
	 * @return void
	 * @throws \App\Domain\Cookie\Exception\NameUniquenessException
	 */
	public function __invoke(CookieId $cookieId, Name $name, CookieProviderId $cookieProviderId, CategoryId $categoryId): void;
}
