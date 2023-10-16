<?php

declare(strict_types=1);

namespace App\Web\Utils;

use App\Application\GlobalSettings\EnabledEnvironmentsResolver;
use App\Domain\GlobalSettings\ValueObject\Environment;
use App\Domain\GlobalSettings\ValueObject\EnvironmentSettings;
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
        EnvironmentSettings $environmentSettings,
        ProjectEnvironments $projectEnvironments,
        Translator $translator,
        ?Closure $additionalMapper = null,
    ): array {
        $environments = EnabledEnvironmentsResolver::resolveProjectEnvironments(
            environmentSettings: $environmentSettings,
            projectEnvironments: $projectEnvironments,
        );

        if (1 >= count($environments)) {
            return [];
        }

        $additionalMapper = $additionalMapper ?? fn (object $environment): object => $environment;

        return array_merge(
            [
                $additionalMapper((object) [
                    'code' => null,
                    'name' => $translator->translate('//layout.all_environments'),
                    'color' => '#000000',
                ]),
            ],
            array_values(
                array_map(
                    static fn (Environment $environment): object => $additionalMapper((object) [
                        'code' => $environment->code->value(),
                        'name' => $environment->name->value(),
                        'color' => $environment->color->value(),
                    ]),
                    $environments,
                ),
            ),
        );
    }
}
