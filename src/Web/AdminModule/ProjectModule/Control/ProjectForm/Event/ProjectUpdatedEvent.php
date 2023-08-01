<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event;

use App\Domain\Project\ValueObject\ProjectId;
use Symfony\Contracts\EventDispatcher\Event;

final class ProjectUpdatedEvent extends Event
{
    public function __construct(
        private readonly ProjectId $projectId,
        private readonly string $oldCode,
        private readonly string $newCode,
    ) {}

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
