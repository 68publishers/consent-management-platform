<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Consent\LastConsentDateView;
use App\ReadModel\Consent\ConsentStatisticsView;
use App\ReadModel\Project\ProjectCookieTotalsView;
use App\ReadModel\Consent\CalculateLastConsentDatesQuery;
use App\ReadModel\Project\CalculateProjectCookieTotalsQuery;
use App\ReadModel\Category\FindAllOptionalCategoryCodesQuery;
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
	public function calculateConsentStatistics(array $projectIds, Period $currentPeriod, ?Period $previousPeriod = NULL): MultiProjectConsentStatistics
	{
		$previousPeriod = $previousPeriod ?? $currentPeriod->createPreviousPeriod();
		$statisticsResults = array_fill_keys($projectIds, [0 => NULL, 1 => NULL]);
		$categoryCodes = $this->queryBus->dispatch(FindAllOptionalCategoryCodesQuery::create());
		$result = MultiProjectConsentStatistics::create();

		if (0 >= count($projectIds)) {
			return $result;
		}

		foreach ($this->queryBus->dispatch(CalculateConsentStatisticsPerPeriodQuery::create($projectIds, $categoryCodes, $previousPeriod->startDate(), $previousPeriod->endDate())) as $consentStatisticsView) {
			assert($consentStatisticsView instanceof ConsentStatisticsView);

			$statisticsResults[$consentStatisticsView->projectId->toString()][0] = $consentStatisticsView;
		}

		foreach ($this->queryBus->dispatch(CalculateConsentStatisticsPerPeriodQuery::create($projectIds, $categoryCodes, $currentPeriod->startDate(), $currentPeriod->endDate())) as $consentStatisticsView) {
			assert($consentStatisticsView instanceof ConsentStatisticsView);

			$statisticsResults[$consentStatisticsView->projectId->toString()][1] = $consentStatisticsView;
		}

		foreach ($statisticsResults as $projectId => [$previous, $current]) {
			$previous = $previous ?? ConsentStatisticsView::createEmpty(ProjectId::fromString($projectId));
			$current = $current ?? ConsentStatisticsView::createEmpty(ProjectId::fromString($projectId));

			$previousTotalPositivitySum = $previous->totalPositiveCount + $previous->totalNegativeCount;
			$previousUniquePositivitySum = $previous->uniquePositiveCount + $previous->uniqueNegativeCount;

			$currentTotalPositivitySum = $current->totalPositiveCount + $current->totalNegativeCount;
			$currentUniquePositivitySum = $current->uniquePositiveCount + $current->uniqueNegativeCount;

			$result = $result->withStatistics($projectId, ConsentStatistics::create(
				PeriodStatistics::create($previous->totalConsentsCount, $current->totalConsentsCount),
				PeriodStatistics::create($previous->uniqueConsentsCount, $current->uniqueConsentsCount),
				PeriodStatistics::create(
					(int) round(0 === $previousTotalPositivitySum ? 0 : $previous->totalPositiveCount / $previousTotalPositivitySum * 100),
					(int) round(0 === $currentTotalPositivitySum ? 0 : $current->totalPositiveCount / $currentTotalPositivitySum * 100)
				),
				PeriodStatistics::create(
					(int) round(0 === $previousUniquePositivitySum ? 0 : $previous->uniquePositiveCount / $previousUniquePositivitySum * 100),
					(int) round(0 === $currentUniquePositivitySum ? 0 : $current->uniquePositiveCount / $currentUniquePositivitySum * 100)
				)
			));
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculateCookieStatistics(array $projectIds, DateTimeImmutable $endDate): MultiProjectCookieStatistics
	{
		$result = MultiProjectCookieStatistics::create();

		foreach ($this->queryBus->dispatch(CalculateProjectCookieTotalsQuery::create($projectIds, $endDate)) as $projectCookieTotalsView) {
			assert($projectCookieTotalsView instanceof ProjectCookieTotalsView);

			$result = $result->withStatistics($projectCookieTotalsView->projectId->toString(), CookieStatistics::create(
				$projectCookieTotalsView->providers,
				$projectCookieTotalsView->commonCookies,
				$projectCookieTotalsView->privateCookies
			));
		}

		foreach ($projectIds as $projectId) {
			if (!$result->has($projectId)) {
				$result = $result->withStatistics($projectId, CookieStatistics::create(0, 0, 0));
			}
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculateLastConsentDate(array $projectIds, DateTimeImmutable $endDate): MultiProjectLastConsentDate
	{
		$result = MultiProjectLastConsentDate::create();

		foreach ($this->queryBus->dispatch(CalculateLastConsentDatesQuery::create($projectIds, $endDate)) as $lastConsentDateView) {
			assert($lastConsentDateView instanceof LastConsentDateView);

			$result = $result->withDate($lastConsentDateView->projectId->toString(), $lastConsentDateView->lastConsentDate);
		}

		foreach ($projectIds as $projectId) {
			if (!$result->has($projectId)) {
				$result = $result->withDate($projectId, NULL);
			}
		}

		return $result;
	}
}
