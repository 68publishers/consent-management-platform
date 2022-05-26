<?php

declare(strict_types=1);

namespace App\ReadModel\Category;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns CategoryView or NULL
 */
final class GetCategoryByCodeQuery extends AbstractQuery
{
	/**
	 * @param string $code
	 *
	 * @return static
	 */
	public static function create(string $code): self
	{
		return self::fromParameters([
			'code' => $code,
		]);
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->getParam('code');
	}
}
