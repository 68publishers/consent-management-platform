<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ConsentStatisticsView[]
 */
final class CalculateConsentStatisticsPerPeriodQuery extends AbstractQuery
{
	/**
	 * @param string[]           $projectIds
	 * @param string[]           $categoryCodes
	 * @param \DateTimeInterface $startDate
	 * @param \DateTimeInterface $endDate
	 *
	 * @return static
	 */
	public static function create(array $projectIds, array $categoryCodes, DateTimeInterface $startDate, DateTimeInterface $endDate): self
	{
		return self::fromParameters([
			'project_ids' => $projectIds,
			'category_codes' => $categoryCodes,
			'start_date' => $startDate,
			'end_date' => $endDate,
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
	 * @return array
	 */
	public function categoryCodes(): array
	{
		return $this->getParam('category_codes');
	}

	/**
	 * @return \DateTimeInterface
	 */
	public function startDate(): DateTimeInterface
	{
		return $this->getParam('start_date');
	}

	/**
	 * @return \DateTimeInterface
	 */
	public function endDate(): DateTimeInterface
	{
		return $this->getParam('end_date');
	}
}
