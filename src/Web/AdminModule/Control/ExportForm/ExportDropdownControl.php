<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm;

use App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface;
use App\Web\Ui\Control;

final class ExportDropdownControl extends Control
{
    public function __construct(
        private readonly ExportCallbackInterface $exportCallback,
        private readonly ExportFormControlFactoryInterface $exportFormControlFactory,
    ) {}

    protected function createComponentForm(): ExportFormControl
    {
        return $this->exportFormControlFactory->create($this->exportCallback);
    }
}
