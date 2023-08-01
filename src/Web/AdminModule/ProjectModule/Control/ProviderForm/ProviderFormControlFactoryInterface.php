<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProviderForm;

use App\ReadModel\Project\ProjectView;

interface ProviderFormControlFactoryInterface
{
    public function create(ProjectView $projectView): ProviderFormControl;
}
