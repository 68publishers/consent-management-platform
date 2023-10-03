<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use App\ReadModel\Project\ProjectCookieSuggestionsStatistics;
use DateTimeImmutable;

interface ProjectStatisticsCalculatorInterface
{
    public function calculateConsentStatistics(string $projectId, Period $currentPeriod, ?Period $previousPeriod = null, ?EnvironmentInterface $environment = null): ConsentStatistics;

    public function calculateCookieStatistics(string $projectId, DateTimeImmutable $endDate, ?EnvironmentInterface $environment = null): CookieStatistics;

    public function calculateLastConsentDate(string $projectId, DateTimeImmutable $endDate, ?EnvironmentInterface $environment = null): ?DateTimeImmutable;

    public function calculateCookieSuggestionStatistics(string $projectId): ?ProjectCookieSuggestionsStatistics;
}
