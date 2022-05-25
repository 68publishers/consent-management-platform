<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\CommandHandler;

use App\Domain\GlobalSettings\GlobalSettings;
use App\Domain\GlobalSettings\GlobalSettingsRepositoryInterface;
use App\Domain\GlobalSettings\Command\StoreGlobalSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class StoreGlobalSettingsCommandHandler implements CommandHandlerInterface
{
	private GlobalSettingsRepositoryInterface $globalSettingsRepository;

	/**
	 * @param \App\Domain\GlobalSettings\GlobalSettingsRepositoryInterface $globalSettingsRepository
	 */
	public function __construct(GlobalSettingsRepositoryInterface $globalSettingsRepository)
	{
		$this->globalSettingsRepository = $globalSettingsRepository;
	}

	/**
	 * @param \App\Domain\GlobalSettings\Command\StoreGlobalSettingsCommand $command
	 *
	 * @return void
	 */
	public function __invoke(StoreGlobalSettingsCommand $command): void
	{
		$globalSettings = $this->globalSettingsRepository->get();

		if (!$globalSettings instanceof GlobalSettings) {
			$globalSettings = GlobalSettings::create($command);
		} else {
			$globalSettings->update($command);
		}

		$this->globalSettingsRepository->save($globalSettings);
	}
}
