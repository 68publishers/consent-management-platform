<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\DataProcessor\RowInterface;
use DomainException;
use Psr\Log\LoggerInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Throwable;

abstract class AbstractImporter implements ImporterInterface
{
    public function __construct(
        protected CommandBusInterface $commandBus,
        protected QueryBusInterface $queryBus,
        protected LoggerInterface $logger,
    ) {}

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
                    json_encode($row->data()->toArray()),
                ));
            }

            return RowResult::error($row->index(), $e->getMessage());
        }
    }
}
