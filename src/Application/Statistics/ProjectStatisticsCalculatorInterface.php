<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;

interface ProjectStatisticsCalculatorInterface
{
	/**
	 * @param string[]           $projectIds
	 * @param \DateTimeImmutable $startDate
	 * @param \DateTimeImmutable $endDate
	 *
	 * @return \App\Application\Statistics\MultiProjectConsentPeriodStatistics
	 */
	public function calculateConsentPeriodStatistics(array $projectIds, DateTimeImmutable $startDate, DateTimeImmutable $endDate): MultiProjectConsentPeriodStatistics;
}
