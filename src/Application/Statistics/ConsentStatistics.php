<?php

declare(strict_types=1);

namespace App\Application\Statistics;

final class ConsentStatistics
{
    private PeriodStatistics $totalConsentsStatistics;

    private PeriodStatistics $uniqueConsentsStatistics;

    private PeriodStatistics $totalConsentsPositivityStatistics;

    private PeriodStatistics $uniqueConsentsPositivityStatistics;

    private function __construct() {}

    public static function create(
        PeriodStatistics $totalConsentsStatistics,
        PeriodStatistics $uniqueConsentsStatistics,
        PeriodStatistics $totalConsentsPositivityStatistics,
        PeriodStatistics $uniqueConsentsPositivityStatistics,
    ): self {
        $consentsPeriodStatistics = new self();
        $consentsPeriodStatistics->totalConsentsStatistics = $totalConsentsStatistics;
        $consentsPeriodStatistics->uniqueConsentsStatistics = $uniqueConsentsStatistics;
        $consentsPeriodStatistics->totalConsentsPositivityStatistics = $totalConsentsPositivityStatistics;
        $consentsPeriodStatistics->uniqueConsentsPositivityStatistics = $uniqueConsentsPositivityStatistics;

        return $consentsPeriodStatistics;
    }

    public function totalConsentsStatistics(): PeriodStatistics
    {
        return $this->totalConsentsStatistics;
    }

    public function uniqueConsentsStatistics(): PeriodStatistics
    {
        return $this->uniqueConsentsStatistics;
    }

    public function totalConsentsPositivityStatistics(): PeriodStatistics
    {
        return $this->totalConsentsPositivityStatistics;
    }

    public function uniqueConsentsPositivityStatistics(): PeriodStatistics
    {
        return $this->uniqueConsentsPositivityStatistics;
    }
}
