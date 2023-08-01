<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider;

use App\Domain\CookieProvider\Exception\CodeUniquenessException;
use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CheckCodeUniquenessInterface
{
    /**
     * @throws CodeUniquenessException
     */
    public function __invoke(CookieProviderId $cookieProviderId, Code $code): void;
}
