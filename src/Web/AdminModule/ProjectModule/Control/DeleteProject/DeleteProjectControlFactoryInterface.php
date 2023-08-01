<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject;

use App\ReadModel\Project\ProjectView;

interface DeleteProjectControlFactoryInterface
{
    public function create(ProjectView $projectView): DeleteProjectControl;
}
