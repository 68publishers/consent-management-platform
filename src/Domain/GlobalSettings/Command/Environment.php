<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

final class Environment
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $color,
    ) {}
}
