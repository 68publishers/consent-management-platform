<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\GlobalSettings\ValueObject\Environment as GlobalSettingsEnvironment;
use App\Domain\GlobalSettings\ValueObject\EnvironmentSettings;
use App\Domain\Project\ValueObject\Environment as ProjectEnvironment;
use App\Domain\Project\ValueObject\Environments as ProjectEnvironments;

final class EnabledEnvironmentsResolver
{
    /**
     * @return array<string, GlobalSettingsEnvironment>
     */
    public static function resolveProjectEnvironments(
        EnvironmentSettings $environmentSettings,
        ProjectEnvironments $projectEnvironments,
    ): array {
        $defaultEnvironment = $environmentSettings->defaultEnvironment;

        $resolved = [
            $defaultEnvironment->code->value() => $defaultEnvironment,
        ];

        foreach ($projectEnvironments->all() as $projectEnvironment) {
            assert($projectEnvironment instanceof ProjectEnvironment);

            $environment = $environmentSettings->environments->getByCode($projectEnvironment->value());

            if (null !== $environment) {
                $resolved[$environment->code->value()] = $environment;
            }
        }

        return $resolved;
    }

    /**
     * @return array<string, GlobalSettingsEnvironment>
     */
    public static function resolveAllEnvironments(EnvironmentSettings $environmentSettings): array
    {
        $defaultEnvironment = $environmentSettings->defaultEnvironment;

        $resolved = [
            $defaultEnvironment->code->value() => $defaultEnvironment,
        ];

        foreach ($environmentSettings->environments->all() as $environment) {
            assert($environment instanceof GlobalSettingsEnvironment);

            $resolved[$environment->code->value()] = $environment;
        }

        return $resolved;
    }
}
