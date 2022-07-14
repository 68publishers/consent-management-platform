<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;
use App\ReadModel\Category\CategoryView;
use App\ReadModel\Consent\ConsentTotalsView;
use App\ReadModel\Category\AllCategoriesQuery;
use App\ReadModel\Consent\LastConsentDateView;
use App\ReadModel\Project\ProjectCookieTotalsView;
use App\ReadModel\Consent\CalculateLastConsentDatesQuery;
use App\ReadModel\Project\CalculateProjectCookieTotalsQuery;
use App\ReadModel\Consent\ScrollThroughConsentsPerPeriodQuery;
use App\ReadModel\Consent\CalculateConsentTotalsPerPeriodQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\Batch;

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
	public function calculateConsentPeriodStatistics(array $projectIds, DateTimeImmutable $startDate, DateTimeImmutable $endDate): MultiProjectConsentPeriodStatistics
	{
		$diff = $startDate->diff($endDate);
		$previousEndDate = $startDate->modify('-1 second');
		$previousStartDate = $previousEndDate->sub($diff);

		$consentStatistics = array_fill_keys($projectIds, [
			'total' => [
				'previous' => 0,
				'current' => 0,
			],
			'unique' => [
				'previous' => 0,
				'current' => 0,
			],
		]);

		foreach ($this->queryBus->dispatch(CalculateConsentTotalsPerPeriodQuery::create($projectIds, $previousStartDate, $previousEndDate)) as $previousTotalsView) {
			assert($previousTotalsView instanceof ConsentTotalsView);
			$consentStatistics[$previousTotalsView->projectId->toString()]['total']['previous'] = $previousTotalsView->total;
			$consentStatistics[$previousTotalsView->projectId->toString()]['unique']['previous'] = $previousTotalsView->unique;
		}

		foreach ($this->queryBus->dispatch(CalculateConsentTotalsPerPeriodQuery::create($projectIds, $startDate, $endDate)) as $currentTotalsView) {
			assert($currentTotalsView instanceof ConsentTotalsView);
			$consentStatistics[$currentTotalsView->projectId->toString()]['total']['current'] = $currentTotalsView->total;
			$consentStatistics[$currentTotalsView->projectId->toString()]['unique']['current'] = $currentTotalsView->unique;
		}

		$result = MultiProjectConsentPeriodStatistics::create();

		foreach ($consentStatistics as $projectId => $statistic) {
			$result = $result->withStatistics($projectId, ConsentPeriodStatistics::create(
				PeriodStatistics::create($statistic['total']['previous'], $statistic['total']['current']),
				PeriodStatistics::create($statistic['unique']['previous'], $statistic['unique']['current'])
			));
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculatePositiveConsentPeriodStatistics(array $projectIds, DateTimeImmutable $startDate, DateTimeImmutable $endDate): MultiProjectConsentPeriodStatistics
	{
		$diff = $startDate->diff($endDate);
		$previousEndDate = $startDate->modify('-1 second');
		$previousStartDate = $previousEndDate->sub($diff);

		// fill values
		$consentStatistics = array_fill_keys($projectIds, [
			'total' => [
				'previousPositive' => 0,
				'previousNegative' => 0,
				'currentPositive' => 0,
				'currentNegative' => 0,
			],
			'unique' => [
				'previousPositive' => 0,
				'previousNegative' => 0,
				'currentPositive' => 0,
				'currentNegative' => 0,
			],
		]);

		// consent counter
		$batchSize = 100;
		$watched = $userIdentifiers = [];
		$sumConsents = static function (array $consents, bool $positivity) use (&$watched): int {
			return count(
				array_filter(
					$consents,
					static fn (bool $consent, string $storageName): bool => $consent === $positivity && in_array($storageName, $watched, TRUE),
					ARRAY_FILTER_USE_BOTH
				),
			);
		};

		// find watched categories
		foreach ($this->queryBus->dispatch(AllCategoriesQuery::create()) as $categoryView) {
			assert($categoryView instanceof CategoryView);
			
			if (!$categoryView->necessary) {
				$watched[] = $categoryView->code->value();
			}
		}

		// the previous period
		foreach ($this->queryBus->dispatch(ScrollThroughConsentsPerPeriodQuery::create($projectIds, $previousStartDate, $previousEndDate)->withBatchSize($batchSize)) as $batch) {
			assert($batch instanceof Batch);

			foreach ($batch->results() as $row) {
				$consentStatistics[$row['projectId']]['total']['previousPositive'] += $sumConsents($row['consents'], TRUE);
				$consentStatistics[$row['projectId']]['total']['previousNegative'] += $sumConsents($row['consents'], FALSE);

				if (!isset($userIdentifiers[$row['userIdentifier']])) {
					$consentStatistics[$row['projectId']]['unique']['previousPositive'] += $sumConsents($row['consents'], TRUE);
					$consentStatistics[$row['projectId']]['unique']['previousNegative'] += $sumConsents($row['consents'], FALSE);
					$userIdentifiers[$row['userIdentifier']] = TRUE;
				}
			}
		}

		// the current period
		$userIdentifiers = [];

		foreach ($this->queryBus->dispatch(ScrollThroughConsentsPerPeriodQuery::create($projectIds, $startDate, $endDate)->withBatchSize($batchSize)) as $batch) {
			assert($batch instanceof Batch);

			foreach ($batch->results() as $row) {
				$consentStatistics[$row['projectId']]['total']['currentPositive'] += $sumConsents($row['consents'], TRUE);
				$consentStatistics[$row['projectId']]['total']['currentNegative'] += $sumConsents($row['consents'], FALSE);

				if (!isset($userIdentifiers[$row['userIdentifier']])) {
					$consentStatistics[$row['projectId']]['unique']['currentPositive'] += $sumConsents($row['consents'], TRUE);
					$consentStatistics[$row['projectId']]['unique']['currentNegative'] += $sumConsents($row['consents'], FALSE);
					$userIdentifiers[$row['userIdentifier']] = TRUE;
				}
			}
		}

		// build result
		$result = MultiProjectConsentPeriodStatistics::create();

		foreach ($consentStatistics as $projectId => $statistic) {
			$previousTotal = $statistic['total']['previousPositive'] + $statistic['total']['previousNegative'];
			$currentTotal = $statistic['total']['currentPositive'] + $statistic['total']['currentNegative'];
			$previousUnique = $statistic['unique']['previousPositive'] + $statistic['unique']['previousNegative'];
			$currentUnique = $statistic['unique']['currentPositive'] + $statistic['unique']['currentNegative'];

			$result = $result->withStatistics($projectId, ConsentPeriodStatistics::create(
				PeriodStatistics::create(
					(int) round(0 === $previousTotal ? 0 : $statistic['total']['previousPositive'] / $previousTotal * 100),
					(int) round(0 === $currentTotal ? 0 : $statistic['total']['currentPositive'] / $currentTotal * 100)
				),
				PeriodStatistics::create(
					(int) round(0 === $previousUnique ? 0 : $statistic['unique']['previousPositive'] / $previousUnique * 100),
					(int) round(0 === $currentUnique ? 0 : $statistic['unique']['currentPositive'] / $currentUnique * 100)
				)
			));
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculateCookieStatistics(array $projectIds): MultiProjectCookieStatistics
	{
		$result = MultiProjectCookieStatistics::create();

		foreach ($this->queryBus->dispatch(CalculateProjectCookieTotalsQuery::create($projectIds)) as $projectCookieTotalsView) {
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
	 * @param array $projectIds
	 *
	 * @return \App\Application\Statistics\MultiProjectLastConsentDate
	 */
	public function calculateLastConsentDate(array $projectIds): MultiProjectLastConsentDate
	{
		$result = MultiProjectLastConsentDate::create();

		foreach ($this->queryBus->dispatch(CalculateLastConsentDatesQuery::create($projectIds)) as $lastConsentDateView) {
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
