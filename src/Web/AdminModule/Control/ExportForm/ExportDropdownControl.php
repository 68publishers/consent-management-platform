<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm;

use App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface;
use App\Web\Ui\Control;

final class ExportDropdownControl extends Control
{
    private ExportCallbackInterface $exportCallback;

    private ExportFormControlFactoryInterface $exportFormControlFactory;

    public function __construct(ExportCallbackInterface $exportCallback, ExportFormControlFactoryInterface $exportFormControlFactory)
    {
        $this->exportCallback = $exportCallback;
        $this->exportFormControlFactory = $exportFormControlFactory;
    }

    protected function createComponentForm(): ExportFormControl
    {
        return $this->exportFormControlFactory->create($this->exportCallback);
    }
}
