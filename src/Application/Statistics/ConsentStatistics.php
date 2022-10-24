<?php

declare(strict_types=1);

namespace App\Application\Statistics;

final class ConsentStatistics
{
	private PeriodStatistics $totalConsentsStatistics;

	private PeriodStatistics $uniqueConsentsStatistics;

	private PeriodStatistics $totalConsentsPositivityStatistics;

	private PeriodStatistics $uniqueConsentsPositivityStatistics;

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\Statistics\PeriodStatistics $totalConsentsStatistics
	 * @param \App\Application\Statistics\PeriodStatistics $uniqueConsentsStatistics
	 * @param \App\Application\Statistics\PeriodStatistics $totalConsentsPositivityStatistics
	 * @param \App\Application\Statistics\PeriodStatistics $uniqueConsentsPositivityStatistics
	 *
	 * @return static
	 */
	public static function create(
		PeriodStatistics $totalConsentsStatistics,
		PeriodStatistics $uniqueConsentsStatistics,
		PeriodStatistics $totalConsentsPositivityStatistics,
		PeriodStatistics $uniqueConsentsPositivityStatistics
	): self {
		$consentsPeriodStatistics = new self();
		$consentsPeriodStatistics->totalConsentsStatistics = $totalConsentsStatistics;
		$consentsPeriodStatistics->uniqueConsentsStatistics = $uniqueConsentsStatistics;
		$consentsPeriodStatistics->totalConsentsPositivityStatistics = $totalConsentsPositivityStatistics;
		$consentsPeriodStatistics->uniqueConsentsPositivityStatistics = $uniqueConsentsPositivityStatistics;

		return $consentsPeriodStatistics;
	}

	/**
	 * @return \App\Application\Statistics\PeriodStatistics
	 */
	public function totalConsentsStatistics(): PeriodStatistics
	{
		return $this->totalConsentsStatistics;
	}

	/**
	 * @return \App\Application\Statistics\PeriodStatistics
	 */
	public function uniqueConsentsStatistics(): PeriodStatistics
	{
		return $this->uniqueConsentsStatistics;
	}

	/**
	 * @return \App\Application\Statistics\PeriodStatistics
	 */
	public function totalConsentsPositivityStatistics(): PeriodStatistics
	{
		return $this->totalConsentsPositivityStatistics;
	}

	/**
	 * @return \App\Application\Statistics\PeriodStatistics
	 */
	public function uniqueConsentsPositivityStatistics(): PeriodStatistics
	{
		return $this->uniqueConsentsPositivityStatistics;
	}
}
