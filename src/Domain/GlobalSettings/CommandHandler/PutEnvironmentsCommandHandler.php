<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\CommandHandler;

use App\Domain\GlobalSettings\Command\PutEnvironmentsCommand;
use App\Domain\GlobalSettings\GlobalSettings;
use App\Domain\GlobalSettings\GlobalSettingsRepositoryInterface;
use App\Domain\GlobalSettings\ValueObject\Environment;
use App\Domain\GlobalSettings\ValueObject\Environments;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class PutEnvironmentsCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly GlobalSettingsRepositoryInterface $globalSettingsRepository,
    ) {}

    public function __invoke(PutEnvironmentsCommand $command): void
    {
        $globalSettings = $this->globalSettingsRepository->get();

        if (!$globalSettings instanceof GlobalSettings) {
            $globalSettings = GlobalSettings::createEmpty();
        }

        $environments = Environments::empty();

        foreach ($command->environments() as $environment) {
            $environments = $environments->with(Environment::fromNative([
                'code' => $environment->code,
                'name' => $environment->name,
                'color' => $environment->color,
            ]));
        }

        $globalSettings->updateEnvironments($environments);

        $this->globalSettingsRepository->save($globalSettings);
    }
}
