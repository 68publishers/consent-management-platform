<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;
use App\ReadModel\Project\ProjectCookieSuggestionsStatistics;

interface ProjectStatisticsCalculatorInterface
{
	public function calculateConsentStatistics(string $projectId, Period $currentPeriod, ?Period $previousPeriod = NULL): ConsentStatistics;

	public function calculateCookieStatistics(string $projectId, DateTimeImmutable $endDate): CookieStatistics;

	public function calculateLastConsentDate(string $projectId, DateTimeImmutable $endDate): ?DateTimeImmutable;

	public function calculateCookieSuggestionStatistics(string $projectId): ?ProjectCookieSuggestionsStatistics;
}
