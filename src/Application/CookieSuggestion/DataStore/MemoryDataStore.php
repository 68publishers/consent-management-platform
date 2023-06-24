<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\DataStore;

final class MemoryDataStore implements DataStoreInterface
{
	private array $storage;

	public function store(string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, array $values): void
	{
		$this->storage[$solutionsUniqueId] = [
			'solution_unique_id' => $solutionsUniqueId,
			'solution_type' => $solutionType,
			'values' => $values,
		];
	}

	public function remove(string $solutionsUniqueId): void
	{
		if (isset($this->storage[$solutionsUniqueId])) {
			unset($this->storage[$solutionsUniqueId]);
		}
	}

	public function get(string $solutionsUniqueId): ?array
	{
		return $this->storage[$solutionsUniqueId] ?? NULL;
	}
}
