<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Web\Ui\Form\FormFactoryInterface;
use App\Application\Acl\ProjectCookieProviderResource;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControl;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event\ProviderUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\OtherProvidersFormControl;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\Event\OtherProvidersUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\OtherProvidersFormControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\Event\OtherProvidersFormProcessingFailedEvent;

/**
 * @IsAllowed(resource=ProjectCookieProviderResource::class, privilege=ProjectCookieProviderResource::UPDATE)
 */
final class ProvidersPresenter extends SelectedProjectPresenter
{
	private ProviderFormControlFactoryInterface $providerFormControlFactory;

	private OtherProvidersFormControlFactoryInterface $otherProvidersFormControlFactory;

	/**
	 * @param \App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControlFactoryInterface             $providerFormControlFactory
	 * @param \App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\OtherProvidersFormControlFactoryInterface $otherProvidersFormControlFactory
	 */
	public function __construct(ProviderFormControlFactoryInterface $providerFormControlFactory, OtherProvidersFormControlFactoryInterface $otherProvidersFormControlFactory)
	{
		parent::__construct();

		$this->providerFormControlFactory = $providerFormControlFactory;
		$this->otherProvidersFormControlFactory = $otherProvidersFormControlFactory;
	}

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControl
	 */
	protected function createComponentProviderForm(): ProviderFormControl
	{
		$control = $this->providerFormControlFactory->create($this->projectView);

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(ProviderUpdatedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::success('provider_updated'));
		});

		$control->addEventListener(ProviderFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('provider_updated_failed'));
		});

		return $control;
	}

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\OtherProvidersFormControl
	 */
	protected function createComponentOtherProvidersForm(): OtherProvidersFormControl
	{
		$control = $this->otherProvidersFormControlFactory->create($this->projectView);

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(OtherProvidersUpdatedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::success('other_providers_updated'));
		});

		$control->addEventListener(OtherProvidersFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('other_providers_update_failed'));
		});

		return $control;
	}
}
