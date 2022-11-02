<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;

interface ProjectStatisticsCalculatorInterface
{
	/**
	 * @param string                                  $projectId
	 * @param \App\Application\Statistics\Period      $currentPeriod
	 * @param \App\Application\Statistics\Period|NULL $previousPeriod
	 *
	 * @return \App\Application\Statistics\ConsentStatistics
	 */
	public function calculateConsentStatistics(string $projectId, Period $currentPeriod, ?Period $previousPeriod = NULL): ConsentStatistics;

	/**
	 * @param string             $projectId
	 * @param \DateTimeImmutable $endDate
	 *
	 * @return \App\Application\Statistics\CookieStatistics
	 */
	public function calculateCookieStatistics(string $projectId, DateTimeImmutable $endDate): CookieStatistics;

	/**
	 * @param string             $projectId
	 * @param \DateTimeImmutable $endDate
	 *
	 * @return \DateTimeImmutable|NULL
	 */
	public function calculateLastConsentDate(string $projectId, DateTimeImmutable $endDate): ?DateTimeImmutable;
}
