<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm;

use App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface;

interface ExportFormControlFactoryInterface
{
    public function create(ExportCallbackInterface $exportCallback): ExportFormControl;
}
