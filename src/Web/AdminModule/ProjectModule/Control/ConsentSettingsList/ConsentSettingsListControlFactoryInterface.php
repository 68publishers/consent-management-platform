<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList;

use App\Domain\Project\ValueObject\ProjectId;

interface ConsentSettingsListControlFactoryInterface
{
	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList\ConsentSettingsListControl
	 */
	public function create(ProjectId $projectId): ConsentSettingsListControl;
}
