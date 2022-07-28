<?php

declare(strict_types=1);

namespace App\Application\Import;

use Psr\Log\LoggerInterface;
use App\Application\DataReader\Reader\ReaderInterface;

interface RunnerInterface
{
	/**
	 * @param \App\Application\DataReader\Reader\ReaderInterface $reader
	 * @param string                                             $describedObjectClassname
	 * @param string                                             $author
	 * @param \Psr\Log\LoggerInterface|NULL                      $logger
	 *
	 * @return \App\Application\Import\ImportState
	 */
	public function run(ReaderInterface $reader, string $describedObjectClassname, string $author, ?LoggerInterface $logger = NULL): ImportState;
}
