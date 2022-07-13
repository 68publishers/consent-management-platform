<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;
use App\ReadModel\Consent\ConsentTotalsView;
use App\ReadModel\Consent\CalculateConsentTotalsPerPeriodQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

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
}
