<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use Nette\InvalidStateException;
use App\Application\Acl\CookieResource;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Application\Acl\CookieProviderResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\ReadModel\CookieProvider\GetCookieProviderByIdQuery;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;

/**
 * @IsAllowed(resource=CookieProviderResource::class, privilege=CookieProviderResource::UPDATE)
 */
final class EditProviderPresenter extends AdminPresenter
{
	private ProviderFormControlFactoryInterface $providerFormControlFactory;

	private CookieListControlFactoryInterface $cookieListControlFactory;

	private CookieFormModalControlFactoryInterface $cookieFormModalControlFactory;

	private QueryBusInterface $queryBus;

	private CookieProviderView $cookieProviderView;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControlFactoryInterface  $providerFormControlFactory
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface      $cookieListControlFactory
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface $cookieFormModalControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                              $queryBus
	 */
	public function __construct(ProviderFormControlFactoryInterface $providerFormControlFactory, CookieListControlFactoryInterface $cookieListControlFactory, CookieFormModalControlFactoryInterface $cookieFormModalControlFactory, QueryBusInterface $queryBus)
	{
		parent::__construct();

		$this->providerFormControlFactory = $providerFormControlFactory;
		$this->cookieListControlFactory = $cookieListControlFactory;
		$this->cookieFormModalControlFactory = $cookieFormModalControlFactory;
		$this->queryBus = $queryBus;
	}

	/**
	 * @param string $id
	 *
	 * @return void
	 * @throws \Nette\Application\AbortException
	 */
	public function actionDefault(string $id): void
	{
		$cookieProviderView = CookieProviderId::isValid($id) ? $this->queryBus->dispatch(GetCookieProviderByIdQuery::create($id)) : NULL;

		if (!$cookieProviderView instanceof CookieProviderView || NULL !== $cookieProviderView->deletedAt || $cookieProviderView->private) {
			$this->subscribeFlashMessage(FlashMessage::warning('provider_not_found'));
			$this->redirect('Providers:');
		}

		$this->cookieProviderView = $cookieProviderView;

		$this->setBreadcrumbItems([
			$this->getPrefixedTranslator()->translate('page_title'),
			$this->cookieProviderView->code->value(),
		]);
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControl
	 */
	protected function createComponentProviderForm(): ProviderFormControl
	{
		$control = $this->providerFormControlFactory->create($this->cookieProviderView);

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(ProviderUpdatedEvent::class, function (ProviderUpdatedEvent $event) {
			$this->subscribeFlashMessage(FlashMessage::success('provider_updated'));

			$this->setBreadcrumbItems([
				$this->getPrefixedTranslator()->translate('page_title'),
				$event->newCode(),
			]);

			$this->redrawControl('heading');
		});

		$control->addEventListener(ProviderFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('provider_update_failed'));
		});

		return $control;
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl
	 */
	protected function createComponentCookieList(): CookieListControl
	{
		if (!$this->getUser()->isAllowed(CookieResource::class, CookieResource::READ)) {
			throw new InvalidStateException('The user is not allowed to read cookies.');
		}

		$control = $this->cookieListControlFactory->create($this->cookieProviderView->id, $this->validLocalesProvider);

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

		$control = $this->cookieFormModalControlFactory->create($this->validLocalesProvider, $this->cookieProviderView->id);
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
