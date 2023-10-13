<?php

declare(strict_types=1);

namespace App\Web\Utils;

use App\Application\GlobalSettings\EnabledEnvironmentsResolver;
use App\Domain\GlobalSettings\ValueObject\Environment;
use App\Domain\GlobalSettings\ValueObject\Environments as GlobalSettingsEnvironments;
use App\Domain\Project\ValueObject\Environments as ProjectEnvironments;
use Closure;
use Nette\Localization\Translator;

/**
 * @phpstan-type EnvironmentOption = object{
 *      code: string|null,
 *      name: string,
 *      color: string,
 *  }
 */
final class ProjectEnvironmentOptions
{
    private function __construct() {}

    /**
     * @param null|Closure(EnvironmentOption $environment): EnvironmentOption $additionalMapper
     *
     * @return array<int, EnvironmentOption>
     */
    public static function create(
        GlobalSettingsEnvironments $globalSettingsEnvironments,
        ProjectEnvironments $projectEnvironments,
        Translator $translator,
        ?Closure $additionalMapper = null,
    ): array {
        $environments = EnabledEnvironmentsResolver::resolveProjectEnvironments(
            globalSettingsEnvironments: $globalSettingsEnvironments,
            projectEnvironments: $projectEnvironments,
        );

        if (0 >= count($environments)) {
            return [];
        }

        $additionalMapper = $additionalMapper ?? fn (object $environment): object => $environment;

        return array_merge(
            [
                $additionalMapper((object) [
                    'code' => '*',
                    'name' => $translator->translate('//layout.all_environments'),
                    'color' => '#000000',
                ]),
            ],
            [
                $additionalMapper((object) [
                    'code' => null,
                    'name' => $translator->translate('//layout.default_environment'),
                    'color' => '#ffffff',
                ]),
            ],
            array_values(
                array_map(
                    static fn (Environment $environment): object => $additionalMapper((object) [
                        'code' => $environment->code,
                        'name' => $environment->name,
                        'color' => $environment->color->value(),
                    ]),
                    $environments,
                ),
            ),
        );
    }
}
