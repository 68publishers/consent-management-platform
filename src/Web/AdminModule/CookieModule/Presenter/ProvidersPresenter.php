<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CookieProviderResource;
use App\Application\CookieProvider\Import\CookieProviderData;
use App\Web\AdminModule\Control\ExportForm\Callback\CookieProvidersExportCallback;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControl;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControl;
use App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use Nette\InvalidStateException;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: CookieProviderResource::class, privilege: CookieProviderResource::READ)]
final class ProvidersPresenter extends AdminPresenter
{
    private ProviderListControlFactoryInterface $providerListControlFactory;

    private ExportDropdownControlFactoryInterface $exportDropdownControlFactory;

    private ImportModalControlFactoryInterface $importModalControlFactory;

    public function __construct(ProviderListControlFactoryInterface $providerListControlFactory, ExportDropdownControlFactoryInterface $exportDropdownControlFactory, ImportModalControlFactoryInterface $importModalControlFactory)
    {
        parent::__construct();

        $this->providerListControlFactory = $providerListControlFactory;
        $this->exportDropdownControlFactory = $exportDropdownControlFactory;
        $this->importModalControlFactory = $importModalControlFactory;
    }

    protected function startup(): void
    {
        parent::startup();

        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentList(): ProviderListControl
    {
        return $this->providerListControlFactory->create();
    }

    protected function createComponentExportDropdown(): ExportDropdownControl
    {
        if (!$this->getUser()->isAllowed(CookieProviderResource::class, CookieProviderResource::EXPORT)) {
            throw new InvalidStateException('The user is not allowed to export providers.');
        }

        return $this->exportDropdownControlFactory->create(new CookieProvidersExportCallback());
    }

    protected function createComponentImport(): ImportModalControl
    {
        $control = $this->importModalControlFactory->create(CookieProviderData::class);

        $control->addEventListener(ShowingImportDetailEvent::class, function () {
            $this->redrawControl('providers_list');
        });

        return $control;
    }
}
