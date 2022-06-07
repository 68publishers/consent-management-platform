<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns CookieProviderView[]
 */
final class CookieProvidersDataGridQuery extends AbstractDataGridQuery
{
	/**
	 * @return static
	 */
	public static function create(): self
	{
		return self::fromParameters([]);
	}
}
