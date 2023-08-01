<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\ReadModel\Project\ProjectView;
use Contributte\MenuControl\IMenuItem;
use App\Application\Acl\ProjectResource;
use Contributte\MenuControl\MenuContainer;
use Contributte\MenuControl\UI\MenuComponent;
use App\ReadModel\Project\FindAllProjectsQuery;
use App\ReadModel\Project\FindUserProjectsQuery;
use App\ReadModel\Project\GetProjectByCodeQuery;
use Nette\Application\ForbiddenRequestException;
use App\Web\AdminModule\Presenter\AdminPresenter;
use Contributte\MenuControl\Config\MenuItemAction;
use App\ReadModel\Project\GetUsersProjectByCodeQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

abstract class SelectedProjectPresenter extends AdminPresenter
{
	private const MENU_NAME_SIDEBAR_PROJECT = 'sidebar_project';

	/** @persistent */
	public string $project = '';
	
	protected QueryBusInterface $queryBus;

	protected ProjectView $projectView;

	private MenuContainer $menuContainer;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 * @param \Contributte\MenuControl\MenuContainer                         $menuContainer
	 *
	 * @return void
	 */
	public function injectProjectDependencies(QueryBusInterface $queryBus, MenuContainer $menuContainer): void
	{
		$this->queryBus = $queryBus;
		$this->menuContainer = $menuContainer;
	}

	/**
	 * @param string $code
	 *
	 * @return void
	 * @throws \Nette\Application\ForbiddenRequestException
	 * @throws \Nette\Application\AbortException
	 */
	public function handleChangeProject(string $code): void
	{
		$this->refreshProjectView($code);
		$this->redirect('this');
	}

	/**
	 * {@inheritdoc}
	 */
	public function checkRequirements($element): void
	{
		parent::checkRequirements($element);

		if (empty($this->project)) {
			throw new ForbiddenRequestException('Project is not selected.');
		}

		$this->refreshProjectView($this->project);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function startup(): void
	{
		parent::startup();

		$this->setLayout('layout.selectedProject');
	}

	/**
	 * @param string $code
	 *
	 * @return void
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	protected function refreshProjectView(string $code): void
	{
		$this->project = $code;
		$projectView = $this->getUser()->isAllowed(ProjectResource::class, ProjectResource::READ_ALL)
			? $this->queryBus->dispatch(GetProjectByCodeQuery::create($code))
			: $this->queryBus->dispatch(GetUsersProjectByCodeQuery::create($code, $this->getIdentity()->id()->toString()));

		if (!$projectView instanceof ProjectView) {
			throw new ForbiddenRequestException('Project not exists or not associated with the current user.');
		}

		$this->projectView = $projectView;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->projectView = $this->projectView;
		$this->template->projectLocales = $this->validLocalesProvider->getValidLocales($this->projectView->locales);
		$this->template->defaultProjectLocale = $this->validLocalesProvider->getValidDefaultLocale($this->projectView->locales);
		$this->template->userProjects = $this->getUser()->isAllowed(ProjectResource::class, ProjectResource::READ_ALL)
			? $this->queryBus->dispatch(FindAllProjectsQuery::create())
			: $this->queryBus->dispatch(FindUserProjectsQuery::create($this->getIdentity()->id()->toString()));
	}

	/**
	 * @return \Contributte\MenuControl\UI\MenuComponent
	 */
	protected function createComponentSidebarProjectMenu(): MenuComponent
	{
		$menu = $this->menuContainer->getMenu(self::MENU_NAME_SIDEBAR_PROJECT);
		$items = $menu->getItems();

		($setupItems = function (array $items) use (&$setupItems) {
			foreach ($items as $item) {
				assert($item instanceof IMenuItem);

				$target = $item->getActionTarget();

				if (NULL !== $target) {
					$item->setAction(MenuItemAction::fromArray([
						'target' => $target,
						'parameters' => array_merge($item->getActionParameters(), [
							'project' => $this->project,
						]),
					]));
				}

				$setupItems($item->getItems());
			}
		})($items);

		$control = new MenuComponent($menu);

		$control->onAnchor[] = function (MenuComponent $component) {
			$component->template->customBreadcrumbItems = $this->customBreadcrumbItems;
		};

		return $control;
	}
}
