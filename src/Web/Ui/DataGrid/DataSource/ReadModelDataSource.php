<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\DataSource;

use Ublaboo\DataGrid\Utils\Sorting;
use App\ReadModel\DataGridQueryInterface;
use Ublaboo\DataGrid\DataSource\IDataSource;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class ReadModelDataSource implements IDataSource
{
	private DataGridQueryInterface $query;

	private QueryBusInterface $queryBus;

	/**
	 * @param \App\ReadModel\DataGridQueryInterface                          $query
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(DataGridQueryInterface $query, QueryBusInterface $queryBus)
	{
		$this->query = $query;
		$this->queryBus = $queryBus;
	}

	/**
	 * @return int
	 */
	public function getCount(): int
	{
		return $this->queryBus->dispatch($this->query->withCountMode());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getData(): array
	{
		return $this->queryBus->dispatch($this->query->withDataMode());
	}


	/**
	 * {@inheritDoc}
	 */
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

	/**
	 * {@inheritDoc}
	 */
	public function filterOne(array $condition): self
	{
		foreach ($condition as $column => $value) {
			$this->query = $this->query->withFilter($column, $value);
		}

		$this->query = $this->query->withOneMode();

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function limit(int $offset, int $limit): self
	{
		$this->query = $this->query->withSize($limit, $offset);

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sort(Sorting $sorting): self
	{
		foreach ($sorting->getSort() as $column => $order) {
			$this->query = $this->query->withSorting($column, $order);
		}

		return $this;
	}
}
