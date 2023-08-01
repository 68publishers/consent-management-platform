<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\DataStore;

interface DataStoreInterface
{
    public function store(string $projectId, string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, string $cookieSuggestionId, array $values): void;

    public function remove(string $projectId, string $solutionsUniqueId): void;

    public function removeAll(string $projectId): void;

    public function get(string $projectId, string $solutionsUniqueId): ?array;

    public function getAll(string $projectId): array;
}
