<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\DataStore;

interface DataStoreInterface
{
	public function store(string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, array $values): void;

	public function remove(string $solutionsUniqueId): void;

	public function get(string $solutionsUniqueId): ?array;
}
