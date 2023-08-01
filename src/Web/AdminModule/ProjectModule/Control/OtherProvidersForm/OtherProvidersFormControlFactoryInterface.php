<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm;

use App\ReadModel\Project\ProjectView;

interface OtherProvidersFormControlFactoryInterface
{
    public function create(ProjectView $projectView): OtherProvidersFormControl;
}
