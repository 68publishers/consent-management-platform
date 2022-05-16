<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\Web\Ui\Presenter;
use Nette\HtmlStringable;
use Nette\Application\UI\Component;
use Contributte\MenuControl\UI\MenuComponent;
use Contributte\MenuControl\UI\IMenuComponentFactory;
use SixtyEightPublishers\SmartNetteComponent\Annotation\LoggedIn;
use SixtyEightPublishers\UserBundle\Bridge\Nette\Security\Identity;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface;

/**
 * @LoggedIn()
 */
abstract class AdminPresenter extends Presenter
{
	private const MENU_NAME_SIDEBAR = 'sidebar';
	private const MENU_NAME_PROFILE = 'profile';

	protected IMenuComponentFactory $menuComponentFactory;

	protected array $customBreadcrumbItems = [];

	private GitRepositoryInterface $gitRepository;

	/**
	 * @param \Contributte\MenuControl\UI\IMenuComponentFactory                       $menuComponentFactory
	 * @param \SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface $gitRepository
	 *
	 * @return void
	 */
	public function injectAdminDependencies(IMenuComponentFactory $menuComponentFactory, GitRepositoryInterface $gitRepository): void
	{
		$this->menuComponentFactory = $menuComponentFactory;
		$this->gitRepository = $gitRepository;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Nette\Application\AbortException
	 */
	protected function onForbiddenRequest(AuthorizationAnnotationInterface $annotation): void
	{
		if ($annotation instanceof LoggedIn) {
			$this->redirect(':Front:SignIn:', [
				'backLink' => $this->storeRequest(),
			]);
		}
	}

	/**
	 * @return void
	 * @throws \SixtyEightPublishers\UserBundle\Application\Exception\IdentityException
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		assert($template instanceof AdminTemplate);

		$template->identity = $this->getIdentity()->data();
		$template->gitRepository = $this->gitRepository;
	}

	/**
	 * @param \Nette\HtmlStringable|string $title
	 *
	 * @return void
	 */
	protected function addBreadcrumbItem($title): void
	{
		assert(is_string($title) || $title instanceof HtmlStringable);

		$this->customBreadcrumbItems[] = $title;
	}

	/**
	 * @param array $items
	 *
	 * @return void
	 */
	protected function setBreadcrumbItems(array $items): void
	{
		$this->customBreadcrumbItems = $items;
	}

	/**
	 * @return \SixtyEightPublishers\UserBundle\Bridge\Nette\Security\Identity
	 */
	protected function getIdentity(): Identity
	{
		$identity = $this->getUser()->getIdentity();
		assert($identity instanceof Identity);

		return $identity;
	}

	/**
	 * @return \Contributte\MenuControl\UI\MenuComponent
	 */
	protected function createComponentSidebarMenu(): MenuComponent
	{
		$control = $this->menuComponentFactory->create(self::MENU_NAME_SIDEBAR);

		$control->onAnchor[] = function (Component $component) {
			$component->template->customBreadcrumbItems = $this->customBreadcrumbItems;
		};

		return $control;
	}

	/**
	 * @return \Contributte\MenuControl\UI\MenuComponent
	 */
	protected function createComponentProfileMenu(): MenuComponent
	{
		return $this->menuComponentFactory->create(self::MENU_NAME_PROFILE);
	}
}
