<?php

declare(strict_types=1);

namespace App\Application\Import;

use Throwable;
use DomainException;
use Psr\Log\LoggerInterface;
use App\Application\DataProcessor\RowInterface;

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
	public function import(array $rows): ImporterResult
	{
		$resolved = [];
		$unresolved = [];
		$importerResult = ImporterResult::of();

		foreach ($rows as $row) {
			foreach ($this->importers as $index => $importer) {
				if ($importer->accepts($row)) {
					$resolved[$index][] = $row;

					continue 2;
				}
			}

			$unresolved[] = $row;
		}

		foreach ($resolved as $index => $r) {
			$importerResult = $importerResult->merge(
				$this->doImport($this->importers[$index], $r)
			);
		}

		if (0 < count($unresolved)) {
			$importerResult = $importerResult->merge(
				$this->createFailedResult($unresolved, 'Can\'t resolve importer for the row')
			);

			$this->logError(self::class, 'Can\'t resolve importer for some rows.', $unresolved);
		}

		return $importerResult;
	}

	/**
	 * @param \App\Application\Import\ImporterInterface $importer
	 * @param array                                     $rows
	 *
	 * @return \App\Application\Import\ImporterResult
	 */
	private function doImport(ImporterInterface $importer, array $rows): ImporterResult
	{
		try {
			return $importer->import($rows);
		} catch (Throwable $e) {
			if (!$e instanceof DomainException) {
				$this->logError(get_class($importer), $e->getMessage(), $rows);
			}

			return $this->createFailedResult($rows, $e->getMessage());
		}
	}

	/**
	 * @param string                                        $importerClassname
	 * @param string                                        $message
	 * @param \App\Application\DataProcessor\RowInterface[] $rows
	 *
	 * @return void
	 */
	private function logError(string $importerClassname, string $message, array $rows): void
	{
		$this->logger->error(sprintf(
			"Error during calling %s::import(): %s\nThe following data have been imported:\n%s",
			$importerClassname,
			$message,
			json_encode(array_map(static fn (RowInterface $row): array => $row->data()->toArray(), $rows))
		));
	}

	/**
	 * @param \App\Application\DataProcessor\RowInterface[] $rows
	 * @param string                                        $message
	 *
	 * @return \App\Application\Import\ImporterResult
	 */
	private function createFailedResult(array $rows, string $message): ImporterResult
	{
		return ImporterResult::of(
			...array_map(static fn (RowInterface $row): RowResult => RowResult::error($row->index(), $message), $rows)
		);
	}
}
