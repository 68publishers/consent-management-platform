<?php

declare(strict_types=1);

namespace App\Application\Statistics;

final class ConsentPeriodStatistics
{
	private PeriodStatistics $totalConsentsPeriodStatistics;

	private PeriodStatistics $uniqueConsentsPeriodStatistics;

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\Statistics\PeriodStatistics $totalConsentsPeriodStatistics
	 * @param \App\Application\Statistics\PeriodStatistics $uniqueConsentsPeriodStatistics
	 *
	 * @return static
	 */
	public static function create(PeriodStatistics $totalConsentsPeriodStatistics, PeriodStatistics $uniqueConsentsPeriodStatistics): self
	{
		$consentsPeriodStatistics = new self();
		$consentsPeriodStatistics->totalConsentsPeriodStatistics = $totalConsentsPeriodStatistics;
		$consentsPeriodStatistics->uniqueConsentsPeriodStatistics = $uniqueConsentsPeriodStatistics;

		return $consentsPeriodStatistics;
	}

	/**
	 * @return \App\Application\Statistics\PeriodStatistics
	 */
	public function totalConsentsPeriodStatistics(): PeriodStatistics
	{
		return $this->totalConsentsPeriodStatistics;
	}

	/**
	 * @return \App\Application\Statistics\PeriodStatistics
	 */
	public function uniqueConsentsPeriodStatistics(): PeriodStatistics
	{
		return $this->uniqueConsentsPeriodStatistics;
	}
}
