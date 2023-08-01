<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\Exception\NameUniquenessException;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\Name;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

interface CheckNameUniquenessInterface
{
    /**
     * @throws NameUniquenessException
     */
    public function __invoke(CookieId $cookieId, Name $name, CookieProviderId $cookieProviderId, CategoryId $categoryId): void;
}
