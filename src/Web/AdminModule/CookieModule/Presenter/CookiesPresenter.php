<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use Nette\InvalidStateException;
use App\Application\Acl\CookieResource;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Application\Cookie\Import\CookieData;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControl;
use App\Web\AdminModule\Control\ExportForm\Callback\CookiesExportCallback;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;

#[Allowed(resource: CookieResource::class, privilege: CookieResource::READ)]
final class CookiesPresenter extends AdminPresenter
{
	private CookieListControlFactoryInterface $cookieListControlFactory;

	private CookieFormModalControlFactoryInterface $cookieFormModalControlFactory;

	private ExportDropdownControlFactoryInterface $exportDropdownControlFactory;

	private ImportModalControlFactoryInterface $importModalControlFactory;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface      $cookieListControlFactory
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface $cookieFormModalControlFactory
	 * @param \App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface               $exportDropdownControlFactory
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface    $importModalControlFactory
	 */
	public function __construct(CookieListControlFactoryInterface $cookieListControlFactory, CookieFormModalControlFactoryInterface $cookieFormModalControlFactory, ExportDropdownControlFactoryInterface $exportDropdownControlFactory, ImportModalControlFactoryInterface $importModalControlFactory)
	{
		parent::__construct();

		$this->cookieListControlFactory = $cookieListControlFactory;
		$this->cookieFormModalControlFactory = $cookieFormModalControlFactory;
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
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl
	 */
	protected function createComponentList(): CookieListControl
	{
		$control = $this->cookieListControlFactory->create($this->validLocalesProvider);

		$control->includeProjectsData(TRUE);
		$control->configureActions(TRUE, TRUE);
		$control->configureAclChecks(CookieResource::class, CookieResource::UPDATE, CookieResource::DELETE);

		return $control;
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl
	 */
	protected function createComponentCookieModal(): CookieFormModalControl
	{
		if (!$this->getUser()->isAllowed(CookieResource::class, CookieResource::CREATE)) {
			throw new InvalidStateException('The user is not allowed to create cookies.');
		}

		$control = $this->cookieFormModalControlFactory->create($this->validLocalesProvider);
		$inner = $control->getInnerControl();

		$inner->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$inner->addEventListener(CookieCreatedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::success('cookie_created'));
			$this->redrawControl('cookie_list');
			$this->closeModal();
		});

		$inner->addEventListener(CookieFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('cookie_creation_failed'));
		});

		return $control;
	}

	/**
	 * @return \App\Web\AdminModule\Control\ExportForm\ExportDropdownControl
	 */
	protected function createComponentExportDropdown(): ExportDropdownControl
	{
		if (!$this->getUser()->isAllowed(CookieResource::class, CookieResource::EXPORT)) {
			throw new InvalidStateException('The user is not allowed to export cookies.');
		}

		return $this->exportDropdownControlFactory->create(new CookiesExportCallback());
	}

	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl
	 */
	protected function createComponentImport(): ImportModalControl
	{
		$control = $this->importModalControlFactory->create(CookieData::class);

		$control->addEventListener(ShowingImportDetailEvent::class, function () {
			$this->redrawControl('cookie_list');
		});

		return $control;
	}
}
