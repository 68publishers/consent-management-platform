<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use Nette\InvalidStateException;
use App\Application\Acl\CookieProviderResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Application\CookieProvider\Import\CookieProviderData;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControl;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl;
use App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControl;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface;
use App\Web\AdminModule\Control\ExportForm\Callback\CookieProvidersExportCallback;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControlFactoryInterface;

/**
 * @IsAllowed(resource=CookieProviderResource::class, privilege=CookieProviderResource::READ)
 */
final class ProvidersPresenter extends AdminPresenter
{
	private ProviderListControlFactoryInterface $providerListControlFactory;

	private ExportDropdownControlFactoryInterface $exportDropdownControlFactory;

	private ImportModalControlFactoryInterface $importModalControlFactory;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControlFactoryInterface $providerListControlFactory
	 * @param \App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface              $exportDropdownControlFactory
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface   $importModalControlFactory
	 */
	public function __construct(ProviderListControlFactoryInterface $providerListControlFactory, ExportDropdownControlFactoryInterface $exportDropdownControlFactory, ImportModalControlFactoryInterface $importModalControlFactory)
	{
		parent::__construct();

		$this->providerListControlFactory = $providerListControlFactory;
		$this->exportDropdownControlFactory = $exportDropdownControlFactory;
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
	 * @return \App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControl
	 */
	protected function createComponentList(): ProviderListControl
	{
		return $this->providerListControlFactory->create();
	}

	/**
	 * @return \App\Web\AdminModule\Control\ExportForm\ExportDropdownControl
	 */
	protected function createComponentExportDropdown(): ExportDropdownControl
	{
		if (!$this->getUser()->isAllowed(CookieProviderResource::class, CookieProviderResource::EXPORT)) {
			throw new InvalidStateException('The user is not allowed to export providers.');
		}

		return $this->exportDropdownControlFactory->create(new CookieProvidersExportCallback());
	}

	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl
	 */
	protected function createComponentImport(): ImportModalControl
	{
		$control = $this->importModalControlFactory->create(CookieProviderData::class);

		$control->addEventListener(ShowingImportDetailEvent::class, function () {
			$this->redrawControl('providers_list');
		});

		return $control;
	}
}
