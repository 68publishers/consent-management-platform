<?php

declare(strict_types=1);

namespace App\ReadModel\Import;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns ImportListView[]
 */
final class ImportDataGridQuery extends AbstractDataGridQuery
{
	/**
	 * @return static
	 */
	public static function create(): self
	{
		return self::fromParameters([]);
	}
}
