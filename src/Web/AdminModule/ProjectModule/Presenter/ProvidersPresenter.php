<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectCookieProviderResource;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\Event\OtherProvidersFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\Event\OtherProvidersUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\OtherProvidersFormControl;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\OtherProvidersFormControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event\ProviderUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControl;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControlFactoryInterface;
use App\Web\Ui\Form\FormFactoryInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectCookieProviderResource::class, privilege: ProjectCookieProviderResource::UPDATE)]
final class ProvidersPresenter extends SelectedProjectPresenter
{
    public function __construct(
        private readonly ProviderFormControlFactoryInterface $providerFormControlFactory,
        private readonly OtherProvidersFormControlFactoryInterface $otherProvidersFormControlFactory,
    ) {
        parent::__construct();
    }

    protected function createComponentProviderForm(): ProviderFormControl
    {
        $control = $this->providerFormControlFactory->create($this->projectView);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
        ]);

        $control->addEventListener(ProviderUpdatedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::success('provider_updated'));
        });

        $control->addEventListener(ProviderFormProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('provider_updated_failed'));
        });

        return $control;
    }

    protected function createComponentOtherProvidersForm(): OtherProvidersFormControl
    {
        $control = $this->otherProvidersFormControlFactory->create($this->projectView);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
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
