<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectCodeChanged extends AbstractDomainEvent
{
    private ProjectId $projectId;

    private Code $code;

    public static function create(ProjectId $projectId, Code $code): self
    {
        $event = self::occur($projectId->toString(), [
            'code' => $code->value(),
        ]);

        $event->projectId = $projectId;
        $event->code = $code;

        return $event;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function code(): Code
    {
        return $this->code;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
        $this->code = Code::fromValue($parameters['code']);
    }
}
