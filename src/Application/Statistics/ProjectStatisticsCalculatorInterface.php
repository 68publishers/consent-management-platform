<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use App\ReadModel\Project\ProjectCookieSuggestionsStatistics;
use DateTimeImmutable;

interface ProjectStatisticsCalculatorInterface
{
    public function calculateConsentStatistics(string $projectId, Period $currentPeriod, ?Period $previousPeriod = null): ConsentStatistics;

    public function calculateCookieStatistics(string $projectId, DateTimeImmutable $endDate): CookieStatistics;

    public function calculateLastConsentDate(string $projectId, DateTimeImmutable $endDate): ?DateTimeImmutable;

    public function calculateCookieSuggestionStatistics(string $projectId): ?ProjectCookieSuggestionsStatistics;
}
