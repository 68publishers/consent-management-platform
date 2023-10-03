<?php

declare(strict_types=1);

namespace App\Application\Statistics;

final class NamedEnvironment implements EnvironmentInterface
{
    public function __construct(
        public readonly string $name,
    ) {}
}
