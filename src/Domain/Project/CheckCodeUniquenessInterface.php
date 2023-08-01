<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\Exception\CodeUniquenessException;
use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\ProjectId;

interface CheckCodeUniquenessInterface
{
    /**
     * @throws CodeUniquenessException
     */
    public function __invoke(ProjectId $projectId, Code $code): void;
}
