<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProviderForm;

use App\ReadModel\Project\ProjectView;

interface ProviderFormControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\Project\ProjectView $projectView
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControl
	 */
	public function create(ProjectView $projectView): ProviderFormControl;
}
