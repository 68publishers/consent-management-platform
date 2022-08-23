<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm;

use App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface;

interface ExportFormControlFactoryInterface
{
	/**
	 * @param \App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface $exportCallback
	 *
	 * @return \App\Web\AdminModule\Control\ExportForm\ExportFormControl
	 */
	public function create(ExportCallbackInterface $exportCallback): ExportFormControl;
}
