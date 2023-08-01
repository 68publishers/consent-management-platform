<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CookieResource;
use App\Application\Cookie\Import\CookieData;
use App\Web\AdminModule\Control\ExportForm\Callback\CookiesExportCallback;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControl;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\Ui\Form\FormFactoryInterface;
use Nette\InvalidStateException;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: CookieResource::class, privilege: CookieResource::READ)]
final class CookiesPresenter extends AdminPresenter
{
    public function __construct(
        private readonly CookieListControlFactoryInterface $cookieListControlFactory,
        private readonly CookieFormModalControlFactoryInterface $cookieFormModalControlFactory,
        private readonly ExportDropdownControlFactoryInterface $exportDropdownControlFactory,
        private readonly ImportModalControlFactoryInterface $importModalControlFactory,
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
        $control = $this->cookieListControlFactory->create($this->validLocalesProvider);

        $control->includeProjectsData(true);
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

    protected function createComponentExportDropdown(): ExportDropdownControl
    {
        if (!$this->getUser()->isAllowed(CookieResource::class, CookieResource::EXPORT)) {
            throw new InvalidStateException('The user is not allowed to export cookies.');
        }

        return $this->exportDropdownControlFactory->create(new CookiesExportCallback());
    }

    protected function createComponentImport(): ImportModalControl
    {
        $control = $this->importModalControlFactory->create(CookieData::class);

        $control->addEventListener(ShowingImportDetailEvent::class, function () {
            $this->redrawControl('cookie_list');
        });

        return $control;
    }
}
