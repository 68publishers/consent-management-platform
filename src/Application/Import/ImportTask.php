<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Bootstrap;
use Spatie\Async\Task;
use App\Application\DataProcessor\RowInterface;

final class ImportTask extends Task
{
	/** @var \App\Application\DataProcessor\RowInterface[] */
	private array $rows;

	private ?ImporterInterface $importer = NULL;

	/**
	 * @param \App\Application\DataProcessor\RowInterface[] $rows
	 * @param \App\Application\Import\ImporterInterface     $importer
	 */
	public function __construct(array $rows, ImporterInterface $importer)
	{
		$this->rows = (static fn (RowInterface ...$rows): array => $rows)(...$rows);
		$this->importer = $importer;
	}

	/**
	 * @return void
	 */
	public function configure(): void
	{
		if (NULL === $this->importer) {
			$this->importer = Bootstrap::boot()
				->createContainer()
				->getByType(ImporterInterface::class);
		}
	}

	/**
	 * @return \App\Application\Import\ImporterResult
	 */
	public function run(): ImporterResult
	{
		return $this->importer->import($this->rows);
	}

	/**
	 * @return string[]
	 */
	public function __sleep(): array
	{
		return ['rows'];
	}
}
