<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\TemplatesForm;

use App\ReadModel\Project\ProjectView;
use App\Application\GlobalSettings\ValidLocalesProvider;

interface TemplatesFormControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\Project\ProjectView                   $projectView
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider $validLocalesProvider
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\TemplatesForm\TemplatesFormControl
	 */
	public function create(ProjectView $projectView, ValidLocalesProvider $validLocalesProvider): TemplatesFormControl;
}
