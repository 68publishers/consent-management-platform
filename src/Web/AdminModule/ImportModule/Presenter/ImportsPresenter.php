<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Presenter;

use App\Application\Acl\ImportResource;
use App\Web\AdminModule\ImportModule\Control\ImportList\ImportListControl;
use App\Web\AdminModule\ImportModule\Control\ImportList\ImportListControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ImportResource::class, privilege: ImportResource::READ)]
final class ImportsPresenter extends AdminPresenter
{
    public function __construct(
        private readonly ImportListControlFactoryInterface $importListControlFactory,
        private readonly ImportModalControlFactoryInterface $importModalControlFactory,
    ) {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();

        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentList(): ImportListControl
    {
        return $this->importListControlFactory->create();
    }

    protected function createComponentImport(): ImportModalControl
    {
        $control = $this->importModalControlFactory->create();

        $control->addEventListener(ShowingImportDetailEvent::class, function () {
            $this->redrawControl('list');
        });

        return $control;
    }
}
