<?php

declare(strict_types=1);

namespace App\ReadModel\Category;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns string[]
 */
final class FindAllOptionalCategoryCodesQuery extends AbstractQuery
{
	/**
	 * @return static
	 */
	public static function create(): self
	{
		return self::fromParameters([]);
	}
}
