<?php

declare(strict_types=1);

namespace App\Domain\Import\CommandHandler;

use App\Domain\Import\Import;
use App\Domain\Import\ImportRepositoryInterface;
use App\Domain\Import\Command\StartImportCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class StartImportCommandHandler implements CommandHandlerInterface
{
	private ImportRepositoryInterface $importRepository;

	/**
	 * @param \App\Domain\Import\ImportRepositoryInterface $importRepository
	 */
	public function __construct(ImportRepositoryInterface $importRepository)
	{
		$this->importRepository = $importRepository;
	}

	/**
	 * @param \App\Domain\Import\Command\StartImportCommand $command
	 *
	 * @return void
	 */
	public function __invoke(StartImportCommand $command): void
	{
		$import = Import::create($command);

		$this->importRepository->save($import);
	}
}
