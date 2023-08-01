<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CookieProviderResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControl;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;

#[Allowed(resource: CookieProviderResource::class, privilege: CookieProviderResource::CREATE)]
final class AddProviderPresenter extends AdminPresenter
{
	private ProviderFormControlFactoryInterface $providerFormControlFactory;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControlFactoryInterface $providerFormControlFactory
	 */
	public function __construct(ProviderFormControlFactoryInterface $providerFormControlFactory)
	{
		parent::__construct();

		$this->providerFormControlFactory = $providerFormControlFactory;
	}

	/**
	 * @return void
	 */
	public function actionDefault(): void
	{
		$this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControl
	 */
	protected function createComponentProviderForm(): ProviderFormControl
	{
		$control = $this->providerFormControlFactory->create();

		$control->addEventListener(ProviderCreatedEvent::class, function (ProviderCreatedEvent $event) {
			$this->subscribeFlashMessage(FlashMessage::success('provider_created'));
			$this->redirect('EditProvider:', ['id' => $event->cookieProviderId()->toString()]);
		});

		$control->addEventListener(ProviderFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('provider_creation_failed'));
		});

		return $control;
	}
}
