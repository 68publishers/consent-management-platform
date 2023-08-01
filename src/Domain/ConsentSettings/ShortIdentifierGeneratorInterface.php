<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use App\Domain\ConsentSettings\Exception\ShortIdentifierGeneratorException;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\Domain\Project\ValueObject\ProjectId;

interface ShortIdentifierGeneratorInterface
{
    /**
     * @throws ShortIdentifierGeneratorException
     */
    public function generate(ProjectId $projectId): ShortIdentifier;
}
