<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use Nette\InvalidStateException;
use App\Application\Acl\CookieResource;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControl;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\Control\ExportForm\Callback\CookiesExportCallback;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;

/**
 * @IsAllowed(resource=CookieResource::class, privilege=CookieResource::READ)
 */
final class CookiesPresenter extends AdminPresenter
{
	private CookieListControlFactoryInterface $cookieListControlFactory;

	private CookieFormModalControlFactoryInterface $cookieFormModalControlFactory;

	private ExportDropdownControlFactoryInterface $exportDropdownControlFactory;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface      $cookieListControlFactory
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface $cookieFormModalControlFactory
	 * @param \App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface               $exportDropdownControlFactory
	 */
	public function __construct(CookieListControlFactoryInterface $cookieListControlFactory, CookieFormModalControlFactoryInterface $cookieFormModalControlFactory, ExportDropdownControlFactoryInterface $exportDropdownControlFactory)
	{
		parent::__construct();

		$this->cookieListControlFactory = $cookieListControlFactory;
		$this->cookieFormModalControlFactory = $cookieFormModalControlFactory;
		$this->exportDropdownControlFactory = $exportDropdownControlFactory;
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
}
