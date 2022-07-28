<?php

declare(strict_types=1);

namespace App\Application\Import;

final class ImportState
{
	public const STATUS_RUNNING = 'running';
	public const STATUS_COMPLETED = 'completed';
	public const STATUS_FAILED = 'failed';

	public string $id;

	public array $importedIndexes = [];

	public array $failedIndexes = [];

	public string $output = '';

	public string $status = self::STATUS_RUNNING;

	/**
	 * @param string $id
	 */
	public function __construct(string $id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function importedTotal(): int
	{
		return count($this->importedIndexes);
	}

	/**
	 * @return int
	 */
	public function failedTotal(): int
	{
		return count($this->failedIndexes);
	}

	/**
	 * @param string $index
	 *
	 * @return void
	 */
	public function addImported(string $index): void
	{
		if (!in_array($index, $this->importedIndexes, TRUE)) {
			$this->importedIndexes[] = $index;
		}
	}

	/**
	 * @param string $index
	 *
	 * @return void
	 */
	public function addFailed(string $index): void
	{
		if (!in_array($index, $this->failedIndexes, TRUE)) {
			$this->failedIndexes[] = $index;
		}
	}

	/**
	 * @return void
	 */
	public function resolveStatus(): void
	{
		if (self::STATUS_RUNNING === $this->status) {
			$this->status = 0 === $this->importedTotal() && 0 < $this->failedTotal() ? self::STATUS_FAILED : self::STATUS_COMPLETED;
		}
	}
}
