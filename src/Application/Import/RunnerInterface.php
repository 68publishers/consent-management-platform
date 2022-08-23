<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\DataProcessor\Read\Reader\ReaderInterface;

interface RunnerInterface
{
	/**
	 * @param \App\Application\DataProcessor\Read\Reader\ReaderInterface $reader
	 * @param \App\Application\Import\ImportOptions                      $options
	 *
	 * @return \App\Application\Import\ImportState
	 */
	public function run(ReaderInterface $reader, ImportOptions $options): ImportState;
}
