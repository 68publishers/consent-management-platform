<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\Description;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectDescriptionChanged extends AbstractDomainEvent
{
    private ProjectId $projectId;

    private Description $description;

    public static function create(ProjectId $projectId, Description $description): self
    {
        $event = self::occur($projectId->toString(), [
            'description' => $description->value(),
        ]);

        $event->projectId = $projectId;
        $event->description = $description;

        return $event;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function description(): Description
    {
        return $this->description;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
        $this->description = Description::fromValue($parameters['description']);
    }
}
