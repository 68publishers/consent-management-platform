<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\DataReader\RowInterface;

interface ImporterInterface
{
	/**
	 * @param \App\Application\DataReader\RowInterface $row
	 *
	 * @return bool
	 */
	public function accepts(RowInterface $row): bool;

	/**
	 * @param \App\Application\DataReader\RowInterface[] $rows
	 *
	 * @return \App\Application\Import\ImporterResult
	 */
	public function import(array $rows): ImporterResult;
}
