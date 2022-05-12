<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\CommandHandler;

use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\ConsentSettings\ConsentSettings;
use App\Domain\ConsentSettings\ValueObject\Settings;
use App\Domain\ConsentSettings\CheckChecksumNotExistsInterface;
use App\Domain\ConsentSettings\Exception\ChecksumExistsException;
use App\Domain\ConsentSettings\ConsentSettingsRepositoryInterface;
use App\Domain\ConsentSettings\Command\StoreConsentSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class StoreConsentSettingsCommandHandler implements CommandHandlerInterface
{
	private ConsentSettingsRepositoryInterface $consentSettingsRepository;

	private CheckChecksumNotExistsInterface $checkChecksumNotExists;

	/**
	 * @param \App\Domain\ConsentSettings\ConsentSettingsRepositoryInterface $consentSettingsRepository
	 * @param \App\Domain\ConsentSettings\CheckChecksumNotExistsInterface    $checkChecksumNotExists
	 */
	public function __construct(ConsentSettingsRepositoryInterface $consentSettingsRepository, CheckChecksumNotExistsInterface $checkChecksumNotExists)
	{
		$this->consentSettingsRepository = $consentSettingsRepository;
		$this->checkChecksumNotExists = $checkChecksumNotExists;
	}

	/**
	 * @param \App\Domain\ConsentSettings\Command\StoreConsentSettingsCommand $command
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function __invoke(StoreConsentSettingsCommand $command): void
	{
		$projectId = ProjectId::fromString($command->projectId());
		$checksum = Checksum::fromValue($command->checksum());
		$settings = Settings::create($command->setting());

		try {
			$consentSettings = ConsentSettings::create($projectId, $checksum, $settings, $this->checkChecksumNotExists);
		} catch (ChecksumExistsException $e) {
			$consentSettings = $this->consentSettingsRepository->get($e->consentSettingsId());
			$consentSettings->addSettings($settings);
		}

		$this->consentSettingsRepository->save($consentSettings);
	}
}
