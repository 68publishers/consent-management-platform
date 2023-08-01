<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\DataStore;

final class MemoryDataStore implements DataStoreInterface
{
    private array $storage;

    public function store(string $projectId, string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, string $cookieSuggestionId, array $values): void
    {
        $section = $this->getAll($projectId);
        $section[$solutionsUniqueId] = [
            'solutionUniqueId' => $solutionUniqueId,
            'solutionType' => $solutionType,
            'cookieSuggestionId' => $cookieSuggestionId,
            'values' => $values,
        ];
        $this->storage[$projectId] = $section;
    }

    public function remove(string $projectId, string $solutionsUniqueId): void
    {
        if (isset($this->storage[$projectId], $this->storage[$projectId][$solutionsUniqueId])) {
            unset($this->storage[$projectId][$solutionsUniqueId]);
        }
    }

    public function removeAll(string $projectId): void
    {
        if (isset($this->storage[$projectId])) {
            unset($this->storage[$projectId]);
        }
    }

    public function get(string $projectId, string $solutionsUniqueId): ?array
    {
        $section = $this->storage[$projectId] ?? [];

        return $section[$solutionsUniqueId] ?? null;
    }

    public function getAll(string $projectId): array
    {
        return $this->storage[$projectId] ?? [];
    }
}
