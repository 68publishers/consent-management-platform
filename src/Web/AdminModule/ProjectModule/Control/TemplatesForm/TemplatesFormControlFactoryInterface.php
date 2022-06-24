<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\TemplatesForm;

use App\Domain\Project\ValueObject\ProjectId;
use App\Application\GlobalSettings\ValidLocalesProvider;

interface TemplatesFormControlFactoryInterface
{
	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId            $projectId
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider $validLocalesProvider
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\TemplatesForm\TemplatesFormControl
	 */
	public function create(ProjectId $projectId, ValidLocalesProvider $validLocalesProvider): TemplatesFormControl;
}
