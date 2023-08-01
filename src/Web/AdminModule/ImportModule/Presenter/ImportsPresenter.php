<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Presenter;

use App\Application\Acl\ImportResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use App\Web\AdminModule\ImportModule\Control\ImportList\ImportListControl;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\ImportModule\Control\ImportList\ImportListControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface;

#[Allowed(resource: ImportResource::class, privilege: ImportResource::READ)]
final class ImportsPresenter extends AdminPresenter
{
	private ImportListControlFactoryInterface $importListControlFactory;

	private ImportModalControlFactoryInterface $importModalControlFactory;

	/**
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportList\ImportListControlFactoryInterface   $importListControlFactory
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface $importModalControlFactory
	 */
	public function __construct(ImportListControlFactoryInterface $importListControlFactory, ImportModalControlFactoryInterface $importModalControlFactory)
	{
		parent::__construct();

		$this->importListControlFactory = $importListControlFactory;
		$this->importModalControlFactory = $importModalControlFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function startup(): void
	{
		parent::startup();

		$this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
	}

	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportList\ImportListControl
	 */
	protected function createComponentList(): ImportListControl
	{
		return $this->importListControlFactory->create();
	}

	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl
	 */
	protected function createComponentImport(): ImportModalControl
	{
		$control = $this->importModalControlFactory->create();

		$control->addEventListener(ShowingImportDetailEvent::class, function () {
			$this->redrawControl('list');
		});

		return $control;
	}
}
