<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid;

use App\ReadModel\Query\DataGridQueryInterface;

interface DataGridFactoryInterface
{
	/**
	 * @param \App\ReadModel\Query\DataGridQueryInterface|null $query
	 *
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 */
	public function create(?DataGridQueryInterface $query = NULL): DataGrid;
}
