<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\DataProcessor\RowInterface;

interface ImporterInterface
{
    public function accepts(RowInterface $row): bool;

    /**
     * @param array<RowInterface> $rows
     */
    public function import(array $rows): ImporterResult;
}
