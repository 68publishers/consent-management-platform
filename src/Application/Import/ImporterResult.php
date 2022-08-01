<?php

declare(strict_types=1);

namespace App\Application\Import;

final class ImporterResult
{
	/** @var \App\Application\Import\RowResult[]  */
	private array $rows = [];

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\Import\RowResult ...$rows
	 *
	 * @return static
	 */
	public static function of(RowResult ...$rows): self
	{
		$result = new self();
		$result->rows = $rows;

		return $result;
	}

	/**
	 * @param \App\Application\Import\RowResult $rowResult
	 *
	 * @return $this
	 */
	public function with(RowResult $rowResult): self
	{
		$rows = $this->rows;
		$rows[] = $rowResult;

		return self::of(...$rows);
	}

	/**
	 * @param \App\Application\Import\ImporterResult $importerResult
	 *
	 * @return $this
	 */
	public function merge(self $importerResult): self
	{
		$rows = array_merge($this->rows, $importerResult->all());

		return self::of(...$rows);
	}

	/**
	 * @return \App\Application\Import\RowResult[]
	 */
	public function all(): array
	{
		return $this->rows;
	}

	/**
	 * @param callable $callback
	 *
	 * @return void
	 */
	public function each(callable $callback): void
	{
		foreach ($this->rows as $row) {
			$callback($row);
		}
	}
}
