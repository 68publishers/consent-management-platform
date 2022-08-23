<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\DataProcessor\RowInterface;

interface ImporterInterface
{
	/**
	 * @param \App\Application\DataProcessor\RowInterface $row
	 *
	 * @return bool
	 */
	public function accepts(RowInterface $row): bool;

	/**
	 * @param \App\Application\DataProcessor\RowInterface[] $rows
	 *
	 * @return \App\Application\Import\ImporterResult
	 */
	public function import(array $rows): ImporterResult;
}
