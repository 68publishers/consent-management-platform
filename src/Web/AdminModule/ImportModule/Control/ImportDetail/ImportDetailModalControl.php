<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\ReadModel\Import\ImportView;
use App\Web\Ui\Modal\AbstractModalControl;

final class ImportDetailModalControl extends AbstractModalControl
{
	private ImportView $importView;

	private ImportDetailControlFactoryInterface $importDetailControlFactory;

	/**
	 * @param \App\ReadModel\Import\ImportView                                                           $importView
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailControlFactoryInterface $importDetailControlFactory
	 */
	public function __construct(ImportView $importView, ImportDetailControlFactoryInterface $importDetailControlFactory)
	{
		$this->importView = $importView;
		$this->importDetailControlFactory = $importDetailControlFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->importView = $this->importView;
	}

	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailControl
	 */
	protected function createComponentDetail(): ImportDetailControl
	{
		return $this->importDetailControlFactory->create($this->importView);
	}
}
