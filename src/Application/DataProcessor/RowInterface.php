<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

interface RowInterface
{
    public static function create(string $index, RowDataInterface $data): self;

    public function withData(RowDataInterface $data): self;

    public function index(): string;

    public function data(): RowDataInterface;
}
