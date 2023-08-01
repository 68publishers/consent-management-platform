<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportForm;

interface ImportFormControlFactoryInterface
{
    public function create(?string $strictImportType = null): ImportFormControl;
}
