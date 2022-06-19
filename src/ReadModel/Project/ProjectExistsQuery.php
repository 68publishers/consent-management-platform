<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ProjectId instance or FALSE
 */
final class ProjectExistsQuery extends AbstractQuery
{
	/**
	 * @param string $projectId
	 *
	 * @return static
	 */
	public static function byId(string $projectId): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
		]);
	}

	/**
	 * @param string $code
	 *
	 * @return static
	 */
	public static function byCode(string $code): self
	{
		return self::fromParameters([
			'code' => $code,
		]);
	}

	/**
	 * @return string
	 */
	public function projectId(): ?string
	{
		return $this->getParam('project_id');
	}

	/**
	 * @return string|NULL
	 */
	public function code(): ?string
	{
		return $this->getParam('code');
	}
}
