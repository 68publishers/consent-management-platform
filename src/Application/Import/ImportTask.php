<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Bootstrap;
use Spatie\Async\Task;
use App\Application\DataReader\RowInterface;

final class ImportTask extends Task
{
	private RowInterface $row;

	private ?ImporterInterface $importer = NULL;

	/**
	 * @param \App\Application\DataReader\RowInterface  $row
	 * @param \App\Application\Import\ImporterInterface $importer
	 */
	public function __construct(RowInterface $row, ImporterInterface $importer)
	{
		$this->row = $row;
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
		return $this->importer->import($this->row);
	}

	/**
	 * @return string[]
	 */
	public function __sleep(): array
	{
		return ['row'];
	}
}
