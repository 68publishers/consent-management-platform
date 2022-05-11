<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid;

use App\ReadModel\DataGridQueryInterface;

interface DataGridFactoryInterface
{
	/**
	 * @param \App\ReadModel\DataGridQueryInterface|null $query
	 *
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 */
	public function create(?DataGridQueryInterface $query = NULL): DataGrid;
}
