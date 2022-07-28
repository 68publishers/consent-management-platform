<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportForm;

interface ImportFormControlFactoryInterface
{
	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportForm\ImportFormControl
	 */
	public function create(): ImportFormControl;
}
