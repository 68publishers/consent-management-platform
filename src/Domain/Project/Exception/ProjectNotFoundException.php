<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use App\Domain\Project\ValueObject\ProjectId;
use DomainException;

final class ProjectNotFoundException extends DomainException
{
    /**
     * @return static
     */
    public static function withId(ProjectId $id): self
    {
        return new self(sprintf(
            'Project with ID %s not found.',
            $id,
        ));
    }
}
