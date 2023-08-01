<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\Cookie\Exception\CookieNotFoundException;
use App\Domain\Cookie\ValueObject\CookieId;

interface CookieRepositoryInterface
{
    public function save(Cookie $cookie): void;

    /**
     * @throws CookieNotFoundException
     */
    public function get(CookieId $id): Cookie;
}
