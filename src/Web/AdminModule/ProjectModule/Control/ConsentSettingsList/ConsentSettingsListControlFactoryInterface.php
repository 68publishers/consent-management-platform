<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList;

use App\Domain\Project\ValueObject\ProjectId;

interface ConsentSettingsListControlFactoryInterface
{
    public function create(ProjectId $projectId): ConsentSettingsListControl;
}
