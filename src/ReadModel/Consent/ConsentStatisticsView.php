<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ArrayViewData;

final class ConsentStatisticsView extends AbstractView
{
    public int $totalConsentsCount;

    public int $uniqueConsentsCount;

    public int $totalPositiveCount;

    public int $uniquePositiveCount;

    public int $totalNegativeCount;

    public int $uniqueNegativeCount;

    /**
     * @return static
     */
    public static function createEmpty(): self
    {
        return self::fromData(new ArrayViewData([
            'totalConsentsCount' => 0,
            'uniqueConsentsCount' => 0,
            'totalPositiveCount' => 0,
            'uniquePositiveCount' => 0,
            'totalNegativeCount' => 0,
            'uniqueNegativeCount' => 0,
        ]));
    }

    public function jsonSerialize(): array
    {
        return [
            'totalConsentsCount' => $this->totalConsentsCount,
            'uniqueConsentsCount' => $this->uniqueConsentsCount,
            'totalPositiveCount' => $this->totalPositiveCount,
            'uniquePositiveCount' => $this->uniquePositiveCount,
            'totalNegativeCount' => $this->totalNegativeCount,
            'uniqueNegativeCount' => $this->uniqueNegativeCount,
        ];
    }
}
