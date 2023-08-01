<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

interface RowDataInterface
{
    public function has(string|int $column): bool;

    public function get(string|int $column, mixed $default = null): mixed;

    public function toArray(): array;
}
