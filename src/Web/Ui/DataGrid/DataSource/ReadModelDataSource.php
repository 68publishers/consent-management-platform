<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\DataSource;

use App\ReadModel\DataGridQueryInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Utils\Sorting;

final class ReadModelDataSource implements IDataSource
{
    public function __construct(
        private DataGridQueryInterface $query,
        private readonly QueryBusInterface $queryBus,
    ) {}

    public function getCount(): int
    {
        return $this->queryBus->dispatch($this->query->withCountMode());
    }

    public function getData(): array
    {
        return $this->queryBus->dispatch($this->query->withDataMode());
    }

    public function filter(array $filters): void
    {
        foreach ($filters as $filter) {
            if ($filter->isValueSet()) {
                foreach ($filter->getCondition() as $column => $value) {
                    $this->query = $this->query->withFilter($column, $value);
                }
            }
        }
    }

    public function filterOne(array $condition): self
    {
        foreach ($condition as $column => $value) {
            $this->query = $this->query->withFilter($column, $value);
        }

        $this->query = $this->query->withOneMode();

        return $this;
    }

    public function limit(int $offset, int $limit): self
    {
        $this->query = $this->query->withSize($limit, $offset);

        return $this;
    }

    public function sort(Sorting $sorting): self
    {
        foreach ($sorting->getSort() as $column => $order) {
            $this->query = $this->query->withSorting($column, $order);
        }

        return $this;
    }
}
