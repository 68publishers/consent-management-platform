<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns an array of ProjectCookieTotalsView
 */
final class CalculateProjectCookieTotalsQuery extends AbstractQuery
{
	/**
	 * @param string[] $projectIds
	 *
	 * @return static
	 */
	public static function create(array $projectIds): self
	{
		return self::fromParameters([
			'project_ids' => $projectIds,
		]);
	}

	/**
	 * @return string[]
	 */
	public function projectIds(): array
	{
		return $this->getParam('project_ids');
	}
}
