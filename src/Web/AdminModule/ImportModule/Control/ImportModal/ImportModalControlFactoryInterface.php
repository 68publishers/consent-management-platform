<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportModal;

interface ImportModalControlFactoryInterface
{
	/**
	 * @param string|NULL $strictImportType
	 *
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl
	 */
	public function create(?string $strictImportType = NULL): ImportModalControl;
}
