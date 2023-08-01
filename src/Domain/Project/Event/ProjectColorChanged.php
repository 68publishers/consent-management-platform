<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\Color;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectColorChanged extends AbstractDomainEvent
{
    private ProjectId $projectId;

    private Color $color;

    public static function create(ProjectId $projectId, Color $color): self
    {
        $event = self::occur($projectId->toString(), [
            'color' => $color->value(),
        ]);

        $event->projectId = $projectId;
        $event->color = $color;

        return $event;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function color(): Color
    {
        return $this->color;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
        $this->color = Color::fromValue($parameters['color']);
    }
}
