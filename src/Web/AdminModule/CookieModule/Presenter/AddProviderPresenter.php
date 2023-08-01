<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CookieProviderResource;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControl;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: CookieProviderResource::class, privilege: CookieProviderResource::CREATE)]
final class AddProviderPresenter extends AdminPresenter
{
    public function __construct(
        private readonly ProviderFormControlFactoryInterface $providerFormControlFactory,
    ) {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

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
