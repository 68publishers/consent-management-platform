<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Domain\GlobalSettings\ValueObject\Environment as GlobalSettingsEnvironment;
use App\Domain\GlobalSettings\ValueObject\Environments as GlobalSettingsEnvironments;
use App\Domain\Project\ValueObject\Environment as ProjectEnvironment;
use App\Domain\Project\ValueObject\Environments as ProjectEnvironments;

final class EnabledEnvironmentsResolver
{
    /**
     * @return array<string, GlobalSettingsEnvironment>
     */
    public static function resolveProjectEnvironments(
        GlobalSettingsEnvironments $globalSettingsEnvironments,
        ProjectEnvironments $projectEnvironments,
    ): array {
        $resolved = [];

        foreach ($projectEnvironments->all() as $projectEnvironment) {
            assert($projectEnvironment instanceof ProjectEnvironment);

            $environment = $globalSettingsEnvironments->getByCode($projectEnvironment->value());

            if (null !== $environment) {
                $resolved[$environment->code] = $environment;
            }
        }

        return $resolved;
    }
}
