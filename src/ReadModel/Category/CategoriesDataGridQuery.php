<?php

declare(strict_types=1);

namespace App\ReadModel\Category;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns CategoryView[]
 */
final class CategoriesDataGridQuery extends AbstractDataGridQuery
{
	/**
	 * @return static
	 */
	public static function create(): self
	{
		return self::fromParameters([]);
	}
}
