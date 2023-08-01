<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectActiveStateChanged extends AbstractDomainEvent
{
    private ProjectId $projectId;

    private bool $active;

    public static function create(ProjectId $projectId, bool $active): self
    {
        $event = self::occur($projectId->toString(), [
            'active' => $active,
        ]);

        $event->projectId = $projectId;
        $event->active = $active;

        return $event;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function active(): bool
    {
        return $this->active;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
        $this->active = (bool) $parameters['active'];
    }
}
