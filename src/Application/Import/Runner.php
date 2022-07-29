<?php

declare(strict_types=1);

namespace App\Application\Import;

use Throwable;
use Spatie\Async\Pool;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use App\Domain\Import\ValueObject\ImportId;
use App\Application\Import\Logger\ImportLogger;
use App\Domain\Import\Command\FailImportCommand;
use App\Application\Import\Logger\ComposedLogger;
use App\Domain\Import\Command\StartImportCommand;
use App\Domain\Import\Command\CompleteImportCommand;
use App\Application\DataReader\Event\ReaderErrorEvent;
use App\Application\DataReader\Reader\ReaderInterface;
use App\Application\DataReader\AbstractDescribedObject;
use App\Application\DataReader\Exception\ReaderException;
use App\Application\DataReader\Exception\RowValidationException;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class Runner implements RunnerInterface
{
	private CommandBusInterface $commandBus;

	private ImporterInterface $importer;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Application\Import\ImporterInterface                        $importer
	 */
	public function __construct(CommandBusInterface $commandBus, ImporterInterface $importer)
	{
		$this->commandBus = $commandBus;
		$this->importer = $importer;
	}

	/**
	 * {@inheritDoc}
	 */
	public function run(ReaderInterface $reader, string $describedObjectClassname, string $author, ?LoggerInterface $logger = NULL): ImportState
	{
		if (!is_subclass_of($describedObjectClassname, AbstractDescribedObject::class, TRUE)) {
			throw new InvalidArgumentException(sprintf(
				'Class %s must be extended from %s.',
				$describedObjectClassname,
				AbstractDescribedObject::class
			));
		}

		$state = new ImportState(ImportId::new()->toString());
		$logger = new ComposedLogger(new ImportLogger($state), $logger ?? new NullLogger());

		$onError = function (ReaderErrorEvent $event) use ($state, $logger): void {
			$error = $event->error();
			$logger->error($error->getMessage());

			if ($error instanceof ReaderException) {
				$state->status = $state::STATUS_FAILED;
				$event->stop();

				return;
			}

			if ($error instanceof RowValidationException) {
				$state->addFailed($error->rowIndex());
			}
		};

		$pool = (new Pool())
			->forceSynchronous();

		$this->commandBus->dispatch(StartImportCommand::create($state->id, $describedObjectClassname, $author));

		foreach ($reader->read($describedObjectClassname::describe(), $onError) as $row) {
			$pool->add(
				new ImportTask($row, $this->importer)
			)->then(function (ImporterResult $importerResult) use ($row, $state, $logger): void {
				$message = sprintf(
					'[:%s] %s',
					$row->index(),
					$importerResult->message()
				);

				if ($importerResult->ok()) {
					$state->addImported($row->index());
					$logger->info($message);
				} else {
					$state->addFailed($row->index());
					$logger->error($message);
				}

				foreach ($importerResult->warnings() as $warning) {
					$state->warningsTotal++;

					$logger->warning(sprintf(
						'[:%s] %s',
						$row->index(),
						$warning
					));
				}
			})->catch(function (Throwable $e) use ($row, $state, $logger): void {
				$state->addFailed($row->index());
				$logger->error(sprintf(
					'[:%s] %s',
					$row->index(),
					$e->getMessage()
				));
			})->timeout(function () use ($row, $state, $logger): void {
				$state->addFailed($row->index());
				$logger->error(sprintf(
					'[:%s] Import took too long and ended with a timeout.',
					$row->index(),
				));
			});
		}

		$pool->wait();
		$state->resolveStatus();

		if ($state::STATUS_COMPLETED === $state->status) {
			$this->commandBus->dispatch(CompleteImportCommand::create($state->id, $state->importedTotal(), $state->failedTotal(), $state->warningsTotal, $state->output));
		} else {
			$this->commandBus->dispatch(FailImportCommand::create($state->id, $state->importedTotal(), $state->failedTotal(), $state->warningsTotal, $state->output));
		}

		return $state;
	}
}
