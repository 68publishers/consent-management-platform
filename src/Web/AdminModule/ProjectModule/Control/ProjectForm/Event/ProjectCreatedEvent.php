<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event;

use App\Domain\Project\ValueObject\ProjectId;
use Symfony\Contracts\EventDispatcher\Event;

final class ProjectCreatedEvent extends Event
{
    public function __construct(
        private readonly ProjectId $projectId,
        private readonly string $code,
    ) {}

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function code(): string
    {
        return $this->code;
    }
}
