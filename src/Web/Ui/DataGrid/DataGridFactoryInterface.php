<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid;

use App\ReadModel\DataGridQueryInterface;

interface DataGridFactoryInterface
{
    public function create(?DataGridQueryInterface $query = null): DataGrid;
}
