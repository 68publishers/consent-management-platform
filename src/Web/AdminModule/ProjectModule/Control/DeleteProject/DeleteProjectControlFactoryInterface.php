<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject;

use App\ReadModel\Project\ProjectView;

interface DeleteProjectControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\Project\ProjectView $projectView
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\DeleteProject\DeleteProjectControl
	 */
	public function create(ProjectView $projectView): DeleteProjectControl;
}
