<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\TemplatesForm;

use App\Application\GlobalSettings\ValidLocalesProvider;
use App\ReadModel\Project\ProjectView;

interface TemplatesFormControlFactoryInterface
{
    public function create(ProjectView $projectView, ValidLocalesProvider $validLocalesProvider): TemplatesFormControl;
}
