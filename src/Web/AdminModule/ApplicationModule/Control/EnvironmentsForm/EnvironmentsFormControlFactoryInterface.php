<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\EnvironmentsForm;

interface EnvironmentsFormControlFactoryInterface
{
    public function create(): EnvironmentsFormControl;
}
