<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\CountMode;

use App\ReadModel\DataGridQueryInterface;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataSource\ReadModelDataSource;
use Ublaboo\DataGrid\Components\DataGridPaginator\DataGridPaginator;

final readonly class LimitedCountMode implements CountModeInterface
{
    public function __construct(
        private int $limit,
    ) {}

    public function apply(DataGrid $grid): void
    {
        $dataModel = $grid->getDataModel();

        if (null !== $dataModel) {
            $dataModel->onAfterPaginated[] = function (ReadModelDataSource $dataSource) use ($grid): void {
                $paginator = $grid->getPaginator()?->getPaginator();

                if (null === $paginator || $paginator->getItemCount() < $this->limit || !$paginator->isLast()) {
                    return;
                }

                $additionalPages = $grid->page - $paginator->page + 1;
                $itemCount = $paginator->getItemCount() + ($additionalPages * $paginator->getItemsPerPage());
                $paginator->setItemCount($itemCount);

                $dataSource->limit(
                    $paginator->getOffset(),
                    $paginator->getItemsPerPage(),
                );

                $dataSource->onData[] = function (array $data, DataGridQueryInterface $query) use ($paginator): array {
                    if ($query::MODE_DATA !== $query->mode() || count($data) >= $paginator->getItemsPerPage()) {
                        return $data;
                    }

                    $paginator->setItemCount($paginator->getItemCount() - $paginator->getItemsPerPage() - ($paginator->getItemsPerPage() - count($data)));

                    return $data;
                };
            };
        }
    }

    public function getPaginatorClass(): string
    {
        return DataGridPaginator::class;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
