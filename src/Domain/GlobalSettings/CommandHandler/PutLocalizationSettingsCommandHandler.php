<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\CommandHandler;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\GlobalSettings\GlobalSettings;
use App\Domain\Shared\ValueObject\LocalesConfig;
use App\Domain\GlobalSettings\GlobalSettingsRepositoryInterface;
use App\Domain\GlobalSettings\Command\PutLocalizationSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class PutLocalizationSettingsCommandHandler implements CommandHandlerInterface
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
	 * @param \App\Domain\GlobalSettings\Command\PutLocalizationSettingsCommand $command
	 *
	 * @return void
	 */
	public function __invoke(PutLocalizationSettingsCommand $command): void
	{
		$globalSettings = $this->globalSettingsRepository->get();

		if (!$globalSettings instanceof GlobalSettings) {
			$globalSettings = GlobalSettings::createEmpty();
		}

		$locales = Locales::empty();
		$defaultLocale = Locale::fromValue($command->defaultLocale());

		foreach ($command->locales() as $locale) {
			$locales = $locales->with(Locale::fromValue($locale));
		}

		$globalSettings->updateLocalizationSettings(LocalesConfig::create($locales, $defaultLocale));

		$this->globalSettingsRepository->save($globalSettings);
	}
}
