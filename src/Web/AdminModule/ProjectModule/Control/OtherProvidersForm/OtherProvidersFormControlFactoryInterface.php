<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm;

use App\ReadModel\Project\ProjectView;

interface OtherProvidersFormControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\Project\ProjectView $projectView
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\OtherProvidersFormControl
	 */
	public function create(ProjectView $projectView): OtherProvidersFormControl;
}
