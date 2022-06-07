<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider;

use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CheckCodeUniquenessInterface
{
	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\CookieProvider\ValueObject\Code             $code
	 *
	 * @return void
	 * @throws \App\Domain\CookieProvider\Exception\CodeUniquenessException
	 */
	public function __invoke(CookieProviderId $cookieProviderId, Code $code): void;
}
