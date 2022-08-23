<?php

declare(strict_types=1);

namespace App\Application\Import;

use Throwable;
use DomainException;
use Psr\Log\LoggerInterface;
use App\Application\DataProcessor\RowInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

abstract class AbstractImporter implements ImporterInterface
{
	protected CommandBusInterface $commandBus;

	protected QueryBusInterface $queryBus;

	protected LoggerInterface $logger;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \Psr\Log\LoggerInterface                                         $logger
	 */
	public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus, LoggerInterface $logger)
	{
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->logger = $logger;
	}

	/**
	 * @param \App\Application\DataProcessor\RowInterface $row
	 * @param callable                                    $importProcess
	 *
	 * @return \App\Application\Import\RowResult
	 */
	protected function wrapRowImport(RowInterface $row, callable $importProcess): RowResult
	{
		try {
			return $importProcess($row);
		} catch (Throwable $e) {
			if (!$e instanceof DomainException) {
				$this->logger->error(sprintf(
					"Error during calling %s::import(): %s\nThe following data have been imported:\n%s",
					static::class,
					$e->getMessage(),
					json_encode($row->data()->toArray())
				));
			}

			return RowResult::error($row->index(), $e->getMessage());
		}
	}
}
