<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\ReadModel\Project\FindUserProjectsQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class DashboardPresenter extends AdminPresenter
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
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->projects = $this->queryBus->dispatch(FindUserProjectsQuery::create($this->getIdentity()->id()->toString()));
	}
}
