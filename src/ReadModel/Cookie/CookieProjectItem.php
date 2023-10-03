<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

final class CookieProjectItem
{
    public function __construct(
        public readonly string $name,
        public readonly string $color,
    ) {}
}
