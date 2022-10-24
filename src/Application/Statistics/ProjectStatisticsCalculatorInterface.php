<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;

interface ProjectStatisticsCalculatorInterface
{
	/**
	 * @param string[]                                $projectIds
	 * @param \App\Application\Statistics\Period      $currentPeriod
	 * @param \App\Application\Statistics\Period|NULL $previousPeriod
	 *
	 * @return \App\Application\Statistics\MultiProjectConsentStatistics
	 */
	public function calculateConsentStatistics(array $projectIds, Period $currentPeriod, ?Period $previousPeriod = NULL): MultiProjectConsentStatistics;

	/**
	 * @param string[]           $projectIds
	 * @param \DateTimeImmutable $endDate
	 *
	 * @return \App\Application\Statistics\MultiProjectCookieStatistics
	 */
	public function calculateCookieStatistics(array $projectIds, DateTimeImmutable $endDate): MultiProjectCookieStatistics;

	/**
	 * @param string[]           $projectIds
	 * @param \DateTimeImmutable $endDate
	 *
	 * @return \App\Application\Statistics\MultiProjectLastConsentDate
	 */
	public function calculateLastConsentDate(array $projectIds, DateTimeImmutable $endDate): MultiProjectLastConsentDate;
}
