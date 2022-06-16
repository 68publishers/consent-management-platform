<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class FindProjectSelectOptionsQuery extends AbstractQuery
{
	/**
	 * @return static
	 */
	public static function all(): self
	{
		return self::fromParameters([]);
	}

	/**
	 * @param string $userId
	 *
	 * @return static
	 */
	public static function byUser(string $userId): self
	{
		return self::fromParameters([
			'user_id' => $userId,
		]);
	}

	/**
	 * @return string|NULL
	 */
	public function userId(): ?string
	{
		return $this->getParam('user_id');
	}
}
