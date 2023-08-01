<?php

declare(strict_types=1);

namespace App\ReadModel\ConsentSettings;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ConsentSettingsView
 */
final class GetConsentSettingsByIdAndProjectIdQuery extends AbstractQuery
{
    /**
     * @return static
     */
    public static function create(string $id, string $projectId): self
    {
        return self::fromParameters([
            'id' => $id,
            'projectId' => $projectId,
        ]);
    }

    public function id(): string
    {
        return $this->getParam('id');
    }

    public function projectId(): string
    {
        return $this->getParam('projectId');
    }
}
