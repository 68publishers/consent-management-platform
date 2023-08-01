<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

final class ArrayRowData implements RowDataInterface
{
    private array $array;

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(array $array): self
    {
        $data = new self();
        $data->array = $array;

        return $data;
    }

    public function has(string|int $column): bool
    {
        return array_key_exists($column, $this->array);
    }

    public function get(string|int $column, mixed $default = null): mixed
    {
        return $this->array[$column] ?? $default;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
