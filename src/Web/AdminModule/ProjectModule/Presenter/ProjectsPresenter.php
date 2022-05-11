<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\ReadModel\Project\FindUserProjectsQuery;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class ProjectsPresenter extends AdminPresenter
{
	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(QueryBusInterface $queryBus)
	{
		parent::__construct();

		$this->queryBus = $queryBus;
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
}
