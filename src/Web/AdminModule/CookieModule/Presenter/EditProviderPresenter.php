<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CookieProviderResource;
use App\Application\Acl\CookieResource;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\ReadModel\CookieProvider\GetCookieProviderByIdQuery;
use App\ReadModel\Project\GetProjectByCookieProviderQuery;
use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControl;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\ProviderFormControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent as PrivateProviderFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event\ProviderUpdatedEvent as PrivateProviderUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControl as PrivateProviderFormControl;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\ProviderFormControlFactoryInterface as PrivateProviderFormControlFactoryInterface;
use App\Web\Ui\Form\FormFactoryInterface;
use Nette\Application\AbortException;
use Nette\InvalidStateException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: CookieProviderResource::class, privilege: CookieProviderResource::UPDATE)]
final class EditProviderPresenter extends AdminPresenter
{
    private CookieProviderView $cookieProviderView;

    private ?ProjectView $projectView = null;

    public function __construct(
        private readonly ProviderFormControlFactoryInterface $providerFormControlFactory,
        private readonly PrivateProviderFormControlFactoryInterface $privateProviderFormControlFactory,
        private readonly CookieListControlFactoryInterface $cookieListControlFactory,
        private readonly CookieFormModalControlFactoryInterface $cookieFormModalControlFactory,
        private readonly QueryBusInterface $queryBus,
    ) {
        parent::__construct();
    }

    /**
     * @throws AbortException
     */
    public function actionDefault(string $id): void
    {
        $cookieProviderView = CookieProviderId::isValid($id) ? $this->queryBus->dispatch(GetCookieProviderByIdQuery::create($id)) : null;

        if ($cookieProviderView instanceof CookieProviderView && $cookieProviderView->private) {
            $this->projectView = $this->queryBus->dispatch(GetProjectByCookieProviderQuery::create($cookieProviderView->id->toString()));
        }

        if (!$cookieProviderView instanceof CookieProviderView || ($cookieProviderView->private && null === $this->projectView)) {
            $this->subscribeFlashMessage(FlashMessage::warning('provider_not_found'));
            $this->redirect('Providers:');
        }

        $this->cookieProviderView = $cookieProviderView;

        $this->setBreadcrumbItems([
            $this->getPrefixedTranslator()->translate('page_title'),
            $this->cookieProviderView->code->value(),
        ]);
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof EditProviderTemplate);

        $template->cookieProviderView = $this->cookieProviderView;
        $template->projectView = $this->projectView;
    }

    protected function createComponentProviderForm(): ProviderFormControl
    {
        if ($this->cookieProviderView->private) {
            throw new InvalidStateException('Con not create the component because the provider is private.');
        }

        $control = $this->providerFormControlFactory->create($this->cookieProviderView);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
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

    protected function createComponentPrivateProviderForm(): PrivateProviderFormControl
    {
        if (!$this->cookieProviderView->private || null === $this->projectView) {
            throw new InvalidStateException('Con not create the component because the provider is not private.');
        }

        $control = $this->privateProviderFormControlFactory->create($this->projectView);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
        ]);

        $control->addEventListener(PrivateProviderUpdatedEvent::class, function (PrivateProviderUpdatedEvent $event) {
            $this->subscribeFlashMessage(FlashMessage::success('provider_updated'));

            $this->setBreadcrumbItems([
                $this->getPrefixedTranslator()->translate('page_title'),
                $event->newCode(),
            ]);

            $this->redrawControl('heading');
        });

        $control->addEventListener(PrivateProviderFormProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('provider_update_failed'));
        });

        return $control;
    }

    protected function createComponentCookieList(): CookieListControl
    {
        if (!$this->getUser()->isAllowed(CookieResource::class, CookieResource::READ)) {
            throw new InvalidStateException('The user is not allowed to read cookies.');
        }

        $control = $this->cookieListControlFactory->create($this->validLocalesProvider, $this->cookieProviderView->id);

        $control->configureActions(true, true);
        $control->configureAclChecks(CookieResource::class, CookieResource::UPDATE, CookieResource::DELETE);

        return $control;
    }

    protected function createComponentCookieModal(): CookieFormModalControl
    {
        if (!$this->getUser()->isAllowed(CookieResource::class, CookieResource::CREATE)) {
            throw new InvalidStateException('The user is not allowed to create cookies.');
        }

        $control = $this->cookieFormModalControlFactory->create($this->validLocalesProvider);
        $inner = $control->getInnerControl();

        $inner->setCookieProviderId($this->cookieProviderView->id);

        $inner->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
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
