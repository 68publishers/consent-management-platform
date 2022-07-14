<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns an array of ProjectCookieTotalsView
 */
final class CalculateProjectCookieTotalsQuery extends AbstractQuery
{
	/**
	 * @param string[]           $projectIds
	 * @param \DateTimeImmutable $maxDate
	 *
	 * @return static
	 */
	public static function create(array $projectIds, DateTimeImmutable $maxDate): self
	{
		return self::fromParameters([
			'project_ids' => $projectIds,
			'max_date' => $maxDate,
		]);
	}

	/**
	 * @return string[]
	 */
	public function projectIds(): array
	{
		return $this->getParam('project_ids');
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function maxDate(): DateTimeImmutable
	{
		return $this->getParam('max_date');
	}
}
