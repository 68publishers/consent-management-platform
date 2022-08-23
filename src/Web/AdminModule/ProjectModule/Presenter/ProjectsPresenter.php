<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use Nette\InvalidStateException;
use App\Application\Acl\ProjectResource;
use App\ReadModel\Project\FindUserProjectsQuery;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControl;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\Control\ExportForm\Callback\ProjectsExportCallback;
use App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface;

/**
 * @IsAllowed(resource=ProjectResource::class, privilege=ProjectResource::READ)
 */
final class ProjectsPresenter extends AdminPresenter
{
	private QueryBusInterface $queryBus;

	private ExportDropdownControlFactoryInterface $exportDropdownControlFactory;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                $queryBus
	 * @param \App\Web\AdminModule\Control\ExportForm\ExportDropdownControlFactoryInterface $exportDropdownControlFactory
	 */
	public function __construct(QueryBusInterface $queryBus, ExportDropdownControlFactoryInterface $exportDropdownControlFactory)
	{
		parent::__construct();

		$this->queryBus = $queryBus;
		$this->exportDropdownControlFactory = $exportDropdownControlFactory;
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

		$this->template->projects = $this->queryBus->dispatch(FindUserProjectsQuery::create($this->getIdentity()->id()->toString()));
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
}
