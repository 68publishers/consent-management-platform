<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event;

use App\Domain\Project\ValueObject\ProjectId;
use Symfony\Contracts\EventDispatcher\Event;

final class ProjectUpdatedEvent extends Event
{
    private ProjectId $projectId;

    private string $oldCode;

    private string $newCode;

    public function __construct(ProjectId $projectId, string $oldCode, string $newCode)
    {
        $this->projectId = $projectId;
        $this->oldCode = $oldCode;
        $this->newCode = $newCode;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function oldCode(): string
    {
        return $this->oldCode;
    }

    public function newCode(): string
    {
        return $this->newCode;
    }
}
