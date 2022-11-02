<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;
use App\ReadModel\Consent\ConsentStatisticsView;
use App\ReadModel\Project\ProjectCookieTotalsView;
use App\ReadModel\Consent\CalculateLastConsentDateQuery;
use App\ReadModel\Project\CalculateProjectCookieTotalsQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\ReadModel\Consent\CalculateConsentStatisticsPerPeriodQuery;

final class ProjectStatisticsCalculator implements ProjectStatisticsCalculatorInterface
{
	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(QueryBusInterface $queryBus)
	{
		$this->queryBus = $queryBus;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Exception
	 */
	public function calculateConsentStatistics(string $projectId, Period $currentPeriod, ?Period $previousPeriod = NULL): ConsentStatistics
	{
		$previousPeriod = $previousPeriod ?? $currentPeriod->createPreviousPeriod();
		$previousStatistics = $this->queryBus->dispatch(CalculateConsentStatisticsPerPeriodQuery::create($projectId, $previousPeriod->startDate(), $previousPeriod->endDate()));
		$currentStatistics = $this->queryBus->dispatch(CalculateConsentStatisticsPerPeriodQuery::create($projectId, $currentPeriod->startDate(), $currentPeriod->endDate()));

		assert($previousStatistics instanceof ConsentStatisticsView);
		assert($currentStatistics instanceof ConsentStatisticsView);

		$previousTotalPositivitySum = $previousStatistics->totalPositiveCount + $previousStatistics->totalNegativeCount;
		$previousUniquePositivitySum = $previousStatistics->uniquePositiveCount + $previousStatistics->uniqueNegativeCount;

		$currentTotalPositivitySum = $currentStatistics->totalPositiveCount + $currentStatistics->totalNegativeCount;
		$currentUniquePositivitySum = $currentStatistics->uniquePositiveCount + $currentStatistics->uniqueNegativeCount;

		return ConsentStatistics::create(
			PeriodStatistics::create($previousStatistics->totalConsentsCount, $currentStatistics->totalConsentsCount),
			PeriodStatistics::create($previousStatistics->uniqueConsentsCount, $currentStatistics->uniqueConsentsCount),
			PeriodStatistics::create(
				(int) round(0 === $previousTotalPositivitySum ? 0 : $previousStatistics->totalPositiveCount / $previousTotalPositivitySum * 100),
				(int) round(0 === $currentTotalPositivitySum ? 0 : $currentStatistics->totalPositiveCount / $currentTotalPositivitySum * 100)
			),
			PeriodStatistics::create(
				(int) round(0 === $previousUniquePositivitySum ? 0 : $previousStatistics->uniquePositiveCount / $previousUniquePositivitySum * 100),
				(int) round(0 === $currentUniquePositivitySum ? 0 : $currentStatistics->uniquePositiveCount / $currentUniquePositivitySum * 100)
			)
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculateCookieStatistics(string $projectId, DateTimeImmutable $endDate): CookieStatistics
	{
		$totals = $this->queryBus->dispatch(CalculateProjectCookieTotalsQuery::create($projectId, $endDate));
		assert($totals instanceof ProjectCookieTotalsView);

		return CookieStatistics::create(
			$totals->providers,
			$totals->commonCookies,
			$totals->privateCookies
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculateLastConsentDate(string $projectId, DateTimeImmutable $endDate): ?DateTimeImmutable
	{
		return $this->queryBus->dispatch(CalculateLastConsentDateQuery::create($projectId, $endDate));
	}
}
