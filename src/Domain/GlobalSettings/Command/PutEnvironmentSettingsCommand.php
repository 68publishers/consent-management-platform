<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class PutEnvironmentSettingsCommand extends AbstractCommand
{
    /**
     * @param array<int, Environment> $environments
     */
    public static function create(
        string $defaultEnvironmentName,
        string $defaultEnvironmentColor,
        array $environments,
    ): self {
        return self::fromParameters([
            'default_environment_name' => $defaultEnvironmentName,
            'default_environment_color' => $defaultEnvironmentColor,
            'environments' => $environments,
        ]);
    }

    public function defaultEnvironmentName(): string
    {
        return $this->getParam('default_environment_name');
    }

    public function defaultEnvironmentColor(): string
    {
        return $this->getParam('default_environment_color');
    }

    /**
     * @return array<int, Environment>
     */
    public function environments(): array
    {
        return $this->getParam('environments');
    }
}
