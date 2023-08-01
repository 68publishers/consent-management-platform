<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

final class Row implements RowInterface
{
    private string $index;

    private RowDataInterface $data;

    private function __construct() {}

    public static function create(string $index, RowDataInterface $data): self
    {
        $row = new self();
        $row->index = $index;
        $row->data = $data;

        return $row;
    }

    public function withData(RowDataInterface $data): RowInterface
    {
        $row = clone $this;
        $row->data = $data;

        return $row;
    }

    public function index(): string
    {
        return $this->index;
    }

    public function data(): RowDataInterface
    {
        return $this->data;
    }
}
