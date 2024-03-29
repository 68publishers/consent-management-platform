<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event;

use App\Domain\Project\ValueObject\ProjectId;
use Symfony\Contracts\EventDispatcher\Event;

final class ProjectDeletedEvent extends Event
{
    public function __construct(
        private readonly ProjectId $projectId,
    ) {}

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }
}
