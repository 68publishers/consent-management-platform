<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\Presenter\AdminTemplate;

final class ProjectsTemplate extends AdminTemplate
{
    /** @var array<ProjectView> */
    public array $projects;
}
