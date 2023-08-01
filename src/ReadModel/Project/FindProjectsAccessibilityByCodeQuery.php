<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `array<ProjectAccessibilityView>`
 */
final class FindProjectsAccessibilityByCodeQuery extends AbstractQuery
{
    /**
     * @param array<string> $projectCodes
     */
    public static function create(string $userId, array $projectCodes): self
    {
        return self::fromParameters([
            'user_id' => $userId,
            'project_codes' => $projectCodes,
        ]);
    }

    public function userId(): string
    {
        return $this->getParam('user_id');
    }

    /**
     * @return array<string>
     */
    public function projectCodes(): array
    {
        return $this->getParam('project_codes');
    }
}
