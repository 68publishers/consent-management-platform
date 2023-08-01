<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `array<ProjectPermissionView>`
 */
final class FindAllProjectsWithPossibleAssociationWithCookieProviderQuery extends AbstractQuery
{
    public static function create(string $cookieProviderId, ?array $projectCodes): self
    {
        return self::fromParameters([
            'cookie_provider_id' => $cookieProviderId,
            'project_codes' => $projectCodes,
        ]);
    }

    public function cookieProviderId(): string
    {
        return $this->getParam('cookie_provider_id');
    }

    /**
     * @return array<string>|NULL
     */
    public function projectCodes(): ?array
    {
        return $this->getParam('project_codes');
    }
}
