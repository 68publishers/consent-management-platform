<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use Nette\InvalidStateException;
use App\Application\Acl\ProjectResource;
use App\Application\Project\Import\ProjectData;
use App\ReadModel\Project\FindAllProjectsQuery;
use App\ReadModel\Project\FindUserProjectsQuery;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControl;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Web\AdminModule\Control\ExportForm\Callback\ProjectsExportCallback;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface;

#[Allowed(resource: ProjectResource::class, privilege: ProjectResource::READ)]
final class ProjectsPresenter extends AdminPresenter
{
	private QueryBusInterface $queryBus;

	private ExportDropdownControlFactoryInterface $exportDropdownControlFactory;

	private ImportModalControlFactoryInterface $importModalControlFactory;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                           $queryBus
	 * @param \App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface            $exportDropdownControlFactory
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControlFactoryInterface $importModalControlFactory
	 */
	public function __construct(QueryBusInterface $queryBus, ExportDropdownControlFactoryInterface $exportDropdownControlFactory, ImportModalControlFactoryInterface $importModalControlFactory)
	{
		parent::__construct();

		$this->queryBus = $queryBus;
		$this->exportDropdownControlFactory = $exportDropdownControlFactory;
		$this->importModalControlFactory = $importModalControlFactory;
	}

	/**
	 * @return void
	 */
	public function actionDefault(): void
	{
		$this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->projects = $this->getUser()->isAllowed(ProjectResource::class, ProjectResource::READ_ALL)
			? $this->queryBus->dispatch(FindAllProjectsQuery::create())
			: $this->queryBus->dispatch(FindUserProjectsQuery::create($this->getIdentity()->id()->toString()));
	}

	/**
	 * @return \App\Web\AdminModule\Control\ExportForm\ExportDropdownControl
	 */
	protected function createComponentExportDropdown(): ExportDropdownControl
	{
		if (!$this->getUser()->isAllowed(ProjectResource::class, ProjectResource::EXPORT)) {
			throw new InvalidStateException('The user is not allowed to export projects.');
		}

		return $this->exportDropdownControlFactory->create(new ProjectsExportCallback());
	}

	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportModal\ImportModalControl
	 */
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
