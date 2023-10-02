<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentList;

use App\Domain\Project\ValueObject\Environments;
use App\Domain\Project\ValueObject\ProjectId;

interface ConsentListControlFactoryInterface
{
    public function create(ProjectId $projectId, Environments $projectEnvironments): ConsentListControl;
}
