<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractBatchedQuery;

final class FindNotificationReceiversByTypeAndProjectIdsQuery extends AbstractBatchedQuery
{
    /**
     * @param array<string> $projectIdsOnly
     */
    public static function create(string $notificationType, array $projectIdsOnly = []): self
    {
        return self::fromParameters([
            'notification_type' => $notificationType,
            'project_ids_only' => $projectIdsOnly,
        ]);
    }

    public function notificationType(): string
    {
        return $this->getParam('notification_type');
    }

    /**
     * @return array<string>
     */
    public function projectIdsOnly(): array
    {
        return $this->getParam('project_ids_only');
    }
}
