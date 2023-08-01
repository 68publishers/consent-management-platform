<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\CommandHandler;

use App\Domain\GlobalSettings\Command\PutApiCacheSettingsCommand;
use App\Domain\GlobalSettings\GlobalSettings;
use App\Domain\GlobalSettings\GlobalSettingsRepositoryInterface;
use App\Domain\GlobalSettings\ValueObject\ApiCache;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class PutApiCacheSettingsCommandHandler implements CommandHandlerInterface
{
    private GlobalSettingsRepositoryInterface $globalSettingsRepository;

    public function __construct(GlobalSettingsRepositoryInterface $globalSettingsRepository)
    {
        $this->globalSettingsRepository = $globalSettingsRepository;
    }

    public function __invoke(PutApiCacheSettingsCommand $command): void
    {
        $globalSettings = $this->globalSettingsRepository->get();

        if (!$globalSettings instanceof GlobalSettings) {
            $globalSettings = GlobalSettings::createEmpty();
        }

        $globalSettings->updateApiCacheSettings(ApiCache::create($command->cacheControlDirectives(), $command->useEntityTag()));

        $this->globalSettingsRepository->save($globalSettings);
    }
}
