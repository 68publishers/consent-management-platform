<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectCookieResource;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;
use App\Web\Ui\Form\FormFactoryInterface;
use Nette\InvalidStateException;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectCookieResource::class, privilege: ProjectCookieResource::READ)]
final class CookiesPresenter extends SelectedProjectPresenter
{
    public function __construct(
        private readonly CookieListControlFactoryInterface $cookieListControlFactory,
        private readonly CookieFormModalControlFactoryInterface $cookieFormModalControlFactory,
    ) {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();

        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentList(): CookieListControl
    {
        $control = $this->cookieListControlFactory->create(
            $this->validLocalesProvider->withLocalesConfig($this->projectView->locales),
            $this->projectView->cookieProviderId,
        );

        $control->configureActions(true, true);
        $control->configureAclChecks(ProjectCookieResource::class, ProjectCookieResource::UPDATE, ProjectCookieResource::DELETE);

        return $control;
    }

    protected function createComponentCookieModal(): CookieFormModalControl
    {
        if (!$this->getUser()->isAllowed(ProjectCookieResource::class, ProjectCookieResource::CREATE)) {
            throw new InvalidStateException('The user is not allowed to create project\'s cookies.');
        }

        $control = $this->cookieFormModalControlFactory->create(
            $this->validLocalesProvider->withLocalesConfig($this->projectView->locales),
        );
        $inner = $control->getInnerControl();

        $inner->setCookieProviderId($this->projectView->cookieProviderId);

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
