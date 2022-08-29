<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportForm;

interface ImportFormControlFactoryInterface
{
	/**
	 * @param string|NULL $strictImportType
	 *
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportForm\ImportFormControl
	 */
	public function create(?string $strictImportType = NULL): ImportFormControl;
}
