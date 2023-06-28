<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

use InvalidArgumentException;
use App\Application\CookieSuggestion\DataStore\DataStoreInterface;

final class Solutions
{
	private string $projectId;

	private string $cookieSuggestionId;

	private string $uniqueKey;

	private DataStoreInterface $dataStore;

	/** @var non-empty-list<SolutionInterface> */
	private array $solutions;

	/**
	 * @param non-empty-list<string> $compositeKey
	 */
	public function __construct(
		string $projectId,
		string $cookieSuggestionId,
		array $compositeKey,
		DataStoreInterface $dataStore,
		SolutionInterface ...$solutions
	) {
		$this->projectId = $projectId;
		$this->cookieSuggestionId = $cookieSuggestionId;
		$this->dataStore = $dataStore;
		$this->solutions = $solutions;
		$this->uniqueKey = md5(
			implode(
				'__',
				array_merge(
					[$projectId, $cookieSuggestionId],
					$compositeKey
				)
			)
		);
	}

	/**
	 * @return non-empty-list<SolutionInterface>
	 */
	public function all(): array
	{
		return $this->solutions;
	}

	public function reset(): void
	{
		$this->dataStore->remove($this->projectId, $this->uniqueKey);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getSolutionArguments(SolutionInterface $solution): array
	{
		$found = FALSE;

		foreach ($this->all() as $s) {
			if ($s === $solution) {
				$found = TRUE;

				break;
			}
		}

		if (!$found) {
			throw new InvalidArgumentException(sprintf(
				'Passed Solution "%s" is not managed by current Solutions instance.',
				$solution->getType(),
			));
		}

		return [
			'solutionsUniqueId' => $this->uniqueKey,
			'solutionUniqueId' => $solution->getUniqueId(),
			'solutionType' => $solution->getType(),
			'cookieSuggestionId' => $this->cookieSuggestionId,
			'args' => $solution->getArguments(),
		];
	}

	/**
	 * @return array{
	 *     solutions_unique_id: string,
	 *     solution_unique_id: string,
	 *     solution_type: string,
	 *     values: array<string, mixed>
	 * }|null
	 */
	public function getDataForResolving(): ?array
	{
		$data = $this->dataStore->get($this->projectId, $this->uniqueKey);

		if (!is_array($data)) {
			return NULL;
		}

		$solutionUniqueId = $data['solutionUniqueId'] ?? '';
		$solutionType = $data['solutionType'] ?? '';

		foreach ($this->solutions as $solution) {
			if ($solutionType === $solution->getType() && $solutionUniqueId === $solution->getUniqueId()) {
				return [
					'solutionsUniqueId' => $this->uniqueKey,
					'solutionUniqueId' => $solutionUniqueId,
					'solutionType' => $solutionType,
					'cookieSuggestionId' => $data['cookieSuggestionId'],
					'values' => $data['values'] ?? [],
				];
			}
		}

		return NULL;
	}
}
