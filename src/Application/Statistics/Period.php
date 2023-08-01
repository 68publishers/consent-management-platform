<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;

final class Period
{
    private DateTimeImmutable $startDate;

    private DateTimeImmutable $endDate;

    private function __construct() {}

    public static function create(DateTimeImmutable $startDate, DateTimeImmutable $endDate): self
    {
        $period = new self();
        $period->startDate = $startDate;
        $period->endDate = $endDate;

        return $period;
    }

    public function createPreviousPeriod(): self
    {
        $diff = $this->startDate()->diff($this->endDate());
        $previousEndDate = $this->startDate()->modify('-1 second');
        $previousStartDate = $previousEndDate->sub($diff);

        return self::create($previousStartDate, $previousEndDate);
    }

    public function startDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): DateTimeImmutable
    {
        return $this->endDate;
    }
}
