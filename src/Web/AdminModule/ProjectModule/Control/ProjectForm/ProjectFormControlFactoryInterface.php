<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm;

use App\ReadModel\Project\ProjectView;

interface ProjectFormControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\Project\ProjectView|NULL $default
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControl
	 */
	public function create(?ProjectView $default = NULL): ProjectFormControl;
}
