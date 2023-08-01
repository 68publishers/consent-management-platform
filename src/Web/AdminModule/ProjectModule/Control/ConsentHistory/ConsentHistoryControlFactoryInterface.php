<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Project\ValueObject\ProjectId;

interface ConsentHistoryControlFactoryInterface
{
    public function create(ConsentId $consentId, ProjectId $projectId): ConsentHistoryControl;
}
