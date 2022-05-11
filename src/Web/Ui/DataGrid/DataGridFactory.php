<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid;

use App\ReadModel\DataGridQueryInterface;
use App\Web\Ui\DataGrid\DataSource\ReadModelDataSource;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class DataGridFactory implements DataGridFactoryInterface
{
	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(QueryBusInterface $queryBus)
	{
		$this->queryBus = $queryBus;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(?DataGridQueryInterface $query = NULL): DataGrid
	{
		$dataGrid = new DataGrid();

		if (NULL !== $query) {
			$dataGrid->setDataSource(new ReadModelDataSource($query, $this->queryBus));
		}

		$dataGrid->setItemsPerPageList([10, 20, 50], FALSE);
		$dataGrid->setStrictSessionFilterValues(FALSE);

		$dataGrid->setCustomPaginatorTemplate(__DIR__ . '/../templates/datagrid/paginator.latte');

		return $dataGrid;
	}
}
