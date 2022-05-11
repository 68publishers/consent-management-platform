<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ProjectView
 */
final class GetProjectByCodeQuery extends AbstractQuery
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
