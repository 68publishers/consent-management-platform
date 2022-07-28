<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportList;

interface ImportListControlFactoryInterface
{
	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportList\ImportListControl
	 */
	public function create(): ImportListControl;
}
