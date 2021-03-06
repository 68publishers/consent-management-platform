<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use Nette\InvalidStateException;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Application\Acl\ProjectCookieResource;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;

/**
 * @IsAllowed(resource=ProjectCookieResource::class, privilege=ProjectCookieResource::READ)
 */
final class CookiesPresenter extends SelectedProjectPresenter
{
	private CookieListControlFactoryInterface $cookieListControlFactory;

	private CookieFormModalControlFactoryInterface $cookieFormModalControlFactory;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface      $cookieListControlFactory
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface $cookieFormModalControlFactory
	 */
	public function __construct(CookieListControlFactoryInterface $cookieListControlFactory, CookieFormModalControlFactoryInterface $cookieFormModalControlFactory)
	{
		parent::__construct();

		$this->cookieListControlFactory = $cookieListControlFactory;
		$this->cookieFormModalControlFactory = $cookieFormModalControlFactory;
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
		$control = $this->cookieListControlFactory->create(
			$this->projectView->cookieProviderId,
			$this->validLocalesProvider->withLocalesConfig($this->projectView->locales)
		);

		$control->configureAclChecks(ProjectCookieResource::class, ProjectCookieResource::UPDATE, ProjectCookieResource::DELETE);

		return $control;
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl
	 */
	protected function createComponentCookieModal(): CookieFormModalControl
	{
		if (!$this->getUser()->isAllowed(ProjectCookieResource::class, ProjectCookieResource::CREATE)) {
			throw new InvalidStateException('The user is not allowed to create project\'s cookies.');
		}

		$control = $this->cookieFormModalControlFactory->create(
			$this->validLocalesProvider->withLocalesConfig($this->projectView->locales),
			$this->projectView->cookieProviderId
		);
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
}
