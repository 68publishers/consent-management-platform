<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\ReadModel\Project\ProjectView;
use App\Application\Acl\ProjectCookieResource;
use App\Application\Acl\ProjectConsentResource;
use App\ReadModel\Project\FindUserProjectsQuery;
use App\Application\Acl\ProjectCookieProviderResource;
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
	 *
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$projects = $this->queryBus->dispatch(FindUserProjectsQuery::create($this->getIdentity()->id()->toString()));

		$this->template->projectsData = array_map(fn (ProjectView $project) => [
			'code' => $project->code->value(),
			'name' => $project->name->value(),
			'color' => $project->color->value(),
			'links' => [
				'consents' => $this->getUser()->isAllowed(ProjectConsentResource::class, ProjectConsentResource::READ)
					? $this->link(':Admin:Project:Consents:', ['project' => $project->code->value()])
					: NULL,
				'providers' => $this->getUser()->isAllowed(ProjectCookieProviderResource::class, ProjectCookieProviderResource::UPDATE)
					? $this->link(':Admin:Project:Providers:', ['project' => $project->code->value()])
					: NULL,
				'cookies' => $this->getUser()->isAllowed(ProjectCookieResource::class, ProjectCookieResource::READ)
					? $this->link(':Admin:Project:Cookies:', ['project' => $project->code->value()])
					: NULL,
			],
		], $projects);
	}
}
