<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class UserProjectsChanged extends AbstractDomainEvent
{
    private UserId $userId;

    /** @var ProjectId[] */
    private array $projectIds;

    /**
     * @return static
     */
    public static function create(UserId $userId, array $projectIds): self
    {
        $event = self::occur($userId->toString(), [
            'project_ids' => array_map(static fn (ProjectId $projectId): string => $projectId->toString(), $projectIds),
        ]);

        $event->userId = $userId;
        $event->projectIds = $projectIds;

        return $event;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return ProjectId[]
     */
    public function projectIds(): array
    {
        return $this->projectIds;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->userId = UserId::fromUuid($this->aggregateId()->id());
        $this->projectIds = array_map(static fn (string $projectId): ProjectId => ProjectId::fromString($projectId), $parameters['project_ids']);
    }
}
