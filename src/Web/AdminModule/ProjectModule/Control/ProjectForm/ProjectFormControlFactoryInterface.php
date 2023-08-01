<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm;

use App\ReadModel\Project\ProjectView;

interface ProjectFormControlFactoryInterface
{
    public function create(?ProjectView $default = null): ProjectFormControl;
}
