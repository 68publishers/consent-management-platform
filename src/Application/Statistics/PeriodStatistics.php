<?php

declare(strict_types=1);

namespace App\Application\Statistics;

final class PeriodStatistics
{
    private int $previousValue;

    private int $currentValue;

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(int $previousValue, int $currentValue): self
    {
        $periodStatistics = new self();
        $periodStatistics->previousValue = $previousValue;
        $periodStatistics->currentValue = $currentValue;

        return $periodStatistics;
    }

    public function previousValue(): int
    {
        return $this->previousValue;
    }

    public function currentValue(): int
    {
        return $this->currentValue;
    }

    public function percentageDiff(): int
    {
        if (0 === $this->previousValue()) {
            return 0 === $this->currentValue() ? 0 : 100;
        }

        return (int) round(($this->currentValue() - $this->previousValue()) / $this->previousValue() * 100);
    }
}
