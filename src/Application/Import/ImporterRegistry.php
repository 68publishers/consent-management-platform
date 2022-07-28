<?php

declare(strict_types=1);

namespace App\Application\Import;

use Throwable;
use DomainException;
use Psr\Log\LoggerInterface;
use App\Application\DataReader\RowInterface;

final class ImporterRegistry implements ImporterInterface
{
	/** @var \App\Application\Import\ImporterInterface[]  */
	private array $importers;

	private LoggerInterface $logger;

	/**
	 * @param \App\Application\Import\ImporterInterface[] $importers
	 * @param \Psr\Log\LoggerInterface                    $logger
	 */
	public function __construct(array $importers, LoggerInterface $logger)
	{
		$this->importers = (static fn (ImporterInterface ...$importers): array => $importers)(...$importers);
		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 */
	public function accepts(RowInterface $row): bool
	{
		foreach ($this->importers as $importer) {
			if ($importer->accepts($row)) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function import(RowInterface $row): ImporterResult
	{
		foreach ($this->importers as $importer) {
			if ($importer->accepts($row)) {
				return $this->doImport($importer, $row);
			}
		}

		$message = 'Can\'t resolve importer for the row';

		$this->logError(self::class, $message, $row);

		return ImporterResult::error($message);
	}

	/**
	 * @param \App\Application\Import\ImporterInterface $importer
	 * @param \App\Application\DataReader\RowInterface  $row
	 *
	 * @return \App\Application\Import\ImporterResult
	 */
	private function doImport(ImporterInterface $importer, RowInterface $row): ImporterResult
	{
		try {
			return $importer->import($row);
		} catch (Throwable $e) {
			if (!$e instanceof DomainException) {
				$this->logError(get_class($importer), $e->getMessage(), $row);
			}

			return ImporterResult::error($e->getMessage());
		}
	}

	/**
	 * @param string                                   $importerClassname
	 * @param string                                   $message
	 * @param \App\Application\DataReader\RowInterface $row
	 *
	 * @return void
	 */
	private function logError(string $importerClassname, string $message, RowInterface $row): void
	{
		$this->logger->error(sprintf(
			"Error during calling %s::import(): %s\nThe row contained the following data:\n%s",
			$importerClassname,
			$message,
			json_encode($row->data()->toArray())
		));
	}
}
