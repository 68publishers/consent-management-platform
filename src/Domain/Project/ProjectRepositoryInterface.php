<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\Exception\ProjectNotFoundException;
use App\Domain\Project\ValueObject\ProjectId;

interface ProjectRepositoryInterface
{
    public function save(Project $project): void;

    /**
     * @throws ProjectNotFoundException
     */
    public function get(ProjectId $id): Project;
}
