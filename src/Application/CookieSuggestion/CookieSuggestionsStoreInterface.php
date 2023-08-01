<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion;

use App\Application\CookieSuggestion\DataStore\DataStoreInterface;
use DateTimeImmutable;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ValueObject\Cookie;

interface CookieSuggestionsStoreInterface
{
    /**
     * @param array<Cookie> $cookies
     */
    public function storeCrawledCookies(string $scenarioName, string $projectId, array $acceptedCategories, DateTimeImmutable $finishedAt, array $cookies): void;

    public function resolveCookieSuggestions(string $projectId): SuggestionsResult;

    public function getDataStore(): DataStoreInterface;
}
