<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

final class DashboardTemplate extends AdminTemplate
{
	/** @var \App\ReadModel\Project\ProjectView[]  */
	public array $projects;
}
