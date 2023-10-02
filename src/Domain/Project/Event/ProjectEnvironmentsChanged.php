<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\Environments;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectEnvironmentsChanged extends AbstractDomainEvent
{
    private ProjectId $projectId;

    private Environments $environments;

    public static function create(ProjectId $projectId, Environments $environments): self
    {
        $event = self::occur($projectId->toString(), [
            'environments' => $environments->toArray(),
        ]);

        $event->projectId = $projectId;
        $event->environments = $environments;

        return $event;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function environments(): Environments
    {
        return $this->environments;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
        $this->environments = Environments::reconstitute($parameters['environments']);
    }
}
