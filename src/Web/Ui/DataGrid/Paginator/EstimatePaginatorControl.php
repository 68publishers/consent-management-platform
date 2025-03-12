<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Paginator;

use Ublaboo\DataGrid\Components\DataGridPaginator\DataGridPaginator;

final class EstimatePaginatorControl extends DataGridPaginator
{
    private ?OptimisticPaginator $paginator = null;

    public function getPaginator(): OptimisticPaginator
    {
        if ($this->paginator === null) {
            $this->paginator = new OptimisticPaginator();
        }

        return $this->paginator;
    }
}
