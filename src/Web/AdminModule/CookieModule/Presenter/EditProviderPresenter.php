<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\ReadModel\CookieProvider\GetCookieProviderByIdQuery;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControl;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;

final class EditProviderPresenter extends AdminPresenter
{
	private ProviderFormControlFactoryInterface $providerFormControlFactory;

	private QueryBusInterface $queryBus;

	private CookieProviderView $cookieProviderView;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControlFactoryInterface $providerFormControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                             $queryBus
	 */
	public function __construct(ProviderFormControlFactoryInterface $providerFormControlFactory, QueryBusInterface $queryBus)
	{
		parent::__construct();

		$this->providerFormControlFactory = $providerFormControlFactory;
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

		if (!$cookieProviderView instanceof CookieProviderView || NULL !== $cookieProviderView->deletedAt) {
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
}
