<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\Web\Ui\Control;
use App\ReadModel\Import\ImportView;

final class ImportDetailControl extends Control
{
	private ImportView $importView;

	/**
	 * @param \App\ReadModel\Import\ImportView $importView
	 */
	public function __construct(ImportView $importView)
	{
		$this->importView = $importView;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->importView = $this->importView;
	}
}
