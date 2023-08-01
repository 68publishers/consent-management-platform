<?php

declare(strict_types=1);

namespace App\Application\Helper;

use DateTimeImmutable;
use Exception;

final class DurationFormatter
{
    public function __construct() {}

    /**
     * @throws Exception
     */
    public static function formatDiff(DateTimeImmutable $start, DateTimeImmutable $end): string
    {
        $interval = $start->diff($end);
        $table = array_filter([
            'y' => $interval->y,
            'mo' => $interval->m,
            'd' => $interval->d,
            'h' => $interval->h,
            'm' => $interval->i,
            's' => $interval->s,
        ], static fn (int $val): bool => $val > 0);

        if (empty($table)) {
            return '< 1s';
        }

        $parts = [];

        foreach ($table as $k => $v) {
            $parts[] = $v . $k;
        }

        return implode(' ', $parts);
    }
}
