<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm;

use App\Web\Ui\Control;
use App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface;

final class ExportDropdownControl extends Control
{
	private ExportCallbackInterface $exportCallback;

	private ExportFormControlFactoryInterface $exportFormControlFactory;

	/**
	 * @param \App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface  $exportCallback
	 * @param \App\Web\AdminModule\Control\ExportForm\ExportFormControlFactoryInterface $exportFormControlFactory
	 */
	public function __construct(ExportCallbackInterface $exportCallback, ExportFormControlFactoryInterface $exportFormControlFactory)
	{
		$this->exportCallback = $exportCallback;
		$this->exportFormControlFactory = $exportFormControlFactory;
	}

	/**
	 * @return \App\Web\AdminModule\Control\ExportForm\ExportFormControl
	 */
	protected function createComponentForm(): ExportFormControl
	{
		return $this->exportFormControlFactory->create($this->exportCallback);
	}
}
