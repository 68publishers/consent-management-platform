<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ProjectView
 */
final class GetUsersProjectByCodeQuery extends AbstractQuery
{
	/**
	 * @param string $code
	 * @param string $userId
	 *
	 * @return static
	 */
	public static function create(string $code, string $userId): self
	{
		return self::fromParameters([
			'code' => $code,
			'user_id' => $userId,
		]);
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->getParam('code');
	}

	/**
	 * @return string
	 */
	public function userId(): string
	{
		return $this->getParam('user_id');
	}
}
