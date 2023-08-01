<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class AssignProjectsToUserCommand extends AbstractCommand
{
    /**
     * @return static
     */
    public static function create(string $userId, array $projectIds): self
    {
        return self::fromParameters([
            'user_id' => $userId,
            'project_ids' => $projectIds,
        ]);
    }

    public function userId(): string
    {
        return $this->getParam('user_id');
    }

    public function projectIds(): array
    {
        return $this->getParam('project_ids');
    }
}
