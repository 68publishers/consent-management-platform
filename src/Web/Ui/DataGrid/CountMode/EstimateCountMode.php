<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\CountMode;

use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataSource\ReadModelDataSource;
use App\Web\Ui\DataGrid\Paginator\EstimatePaginatorControl;

final class EstimateCountMode implements CountModeInterface
{
    private ?int $currentRowsCount = null;

    public function apply(DataGrid $grid): void
    {
        $dataSource = $grid->getDataSource();
        assert($dataSource instanceof ReadModelDataSource);

        $dataSource->onData[] = function (array $data) {
            $this->currentRowsCount = count($data);

            return $data;
        };
    }

    public function getPaginatorClass(): string
    {
        return EstimatePaginatorControl::class;
    }

    public function getCurrentRowsCount(): ?int
    {
        return $this->currentRowsCount;
    }
}
