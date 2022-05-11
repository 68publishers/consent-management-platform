<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Web\AdminModule\Presenter\AdminTemplate;

final class ProjectsTemplate extends AdminTemplate
{
	/** @var \App\ReadModel\Project\ProjectView[]  */
	public array $projects;
}
