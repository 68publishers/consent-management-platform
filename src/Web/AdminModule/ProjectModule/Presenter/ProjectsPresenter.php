<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectResource;
use App\Application\Project\Import\ProjectData;
use App\ReadModel\Project\FindAllProjectsQuery;
use App\ReadModel\Project\FindUserProjectsQuery;
use App\Web\AdminModule\Control\ExportForm\Callback\ProjectsExportCallback;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControl;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use Nette\InvalidStateException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectResource::class, privilege: ProjectResource::READ)]
final class ProjectsPresenter extends AdminPresenter
{
    private QueryBusInterface $queryBus;

    private ExportDropdownControlFactoryInterface $exportDropdownControlFactory;

    private ImportModalControlFactoryInterface $importModalControlFactory;

    public function __construct(QueryBusInterface $queryBus, ExportDropdownControlFactoryInterface $exportDropdownControlFactory, ImportModalControlFactoryInterface $importModalControlFactory)
    {
        parent::__construct();

        $this->queryBus = $queryBus;
        $this->exportDropdownControlFactory = $exportDropdownControlFactory;
        $this->importModalControlFactory = $importModalControlFactory;
    }

    public function actionDefault(): void
    {
        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ProjectsTemplate);

        $template->projects = $this->getUser()->isAllowed(ProjectResource::class, ProjectResource::READ_ALL)
            ? $this->queryBus->dispatch(FindAllProjectsQuery::create())
            : $this->queryBus->dispatch(FindUserProjectsQuery::create($this->getIdentity()->id()->toString()));
    }

    protected function createComponentExportDropdown(): ExportDropdownControl
    {
        if (!$this->getUser()->isAllowed(ProjectResource::class, ProjectResource::EXPORT)) {
            throw new InvalidStateException('The user is not allowed to export projects.');
        }

        return $this->exportDropdownControlFactory->create(new ProjectsExportCallback());
    }

    protected function createComponentImport(): ImportModalControl
    {
        $control = $this->importModalControlFactory->create(ProjectData::class);

        $control->addEventListener(ShowingImportDetailEvent::class, function () {
            $this->redrawControl('before_content');
            $this->redrawControl('projects');
        });

        return $control;
    }
}
