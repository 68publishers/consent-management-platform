<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Checksum;

interface CheckChecksumNotExistsInterface
{
    public function __invoke(ProjectId $projectId, Checksum $checksum): void;
}
