<?php

declare(strict_types=1);

namespace App\Domain\Import;

use App\Domain\Import\ValueObject\ImportId;

interface ImportRepositoryInterface
{
	/**
	 * @param \App\Domain\Import\Import $import
	 *
	 * @return void
	 */
	public function save(Import $import): void;

	/**
	 * @param \App\Domain\Import\ValueObject\ImportId $id
	 *
	 * @return \App\Domain\Import\Import
	 * @throws \App\Domain\Import\Exception\ImportNotFoundException
	 */
	public function get(ImportId $id): Import;
}
