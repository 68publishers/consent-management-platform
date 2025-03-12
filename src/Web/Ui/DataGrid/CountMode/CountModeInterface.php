<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\CountMode;

use App\Web\Ui\DataGrid\DataGrid;
use Ublaboo\DataGrid\Components\DataGridPaginator\DataGridPaginator;

interface CountModeInterface
{
    public function apply(DataGrid $grid): void;

    /**
     * @return class-string<DataGridPaginator>
     */
    public function getPaginatorClass(): string;
}
