<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\Web\Ui\Presenter;
use Nette\HtmlStringable;
use App\ReadModel\User\UserView;
use Nette\Application\AbortException;
use App\Web\Control\Footer\FooterControl;
use Contributte\MenuControl\UI\MenuComponent;
use Contributte\MenuControl\UI\MenuComponentFactory;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Application\Localization\ApplicationDateTimeZone;
use App\Web\Control\Footer\FooterControlFactoryInterface;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\LoggedIn;
use SixtyEightPublishers\UserBundle\Bridge\Nette\Security\Identity;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;

#[LoggedIn]
abstract class AdminPresenter extends Presenter
{
	private const MENU_NAME_SIDEBAR = 'sidebar';
	private const MENU_NAME_PROFILE = 'profile';

	protected GlobalSettingsInterface $globalSettings;

	protected ValidLocalesProvider $validLocalesProvider;

	private MenuComponentFactory $menuComponentFactory;

	protected array $customBreadcrumbItems = [];

	private FooterControlFactoryInterface $footerControlFactory;

	/**
	 * @param \App\Application\GlobalSettings\GlobalSettingsInterface $globalSettings
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider    $validLocalesProvider
	 * @param \Contributte\MenuControl\UI\MenuComponentFactory        $menuComponentFactory
	 * @param \App\Web\Control\Footer\FooterControlFactoryInterface   $footerControlFactory
	 *
	 * @return void
	 */
	public function injectAdminDependencies(GlobalSettingsInterface $globalSettings, ValidLocalesProvider $validLocalesProvider, MenuComponentFactory $menuComponentFactory, FooterControlFactoryInterface $footerControlFactory): void
	{
		$this->globalSettings = $globalSettings;
		$this->validLocalesProvider = $validLocalesProvider;
		$this->menuComponentFactory = $menuComponentFactory;
		$this->footerControlFactory = $footerControlFactory;
	}

	/**
	 * @throws AbortException
	 */
	protected function onForbiddenRequest(ForbiddenRequestException $exception): void
	{
		if ($exception->rule instanceof LoggedIn) {
			$this->redirect(':Front:SignIn:', [
				'backLink' => $this->storeRequest(),
			]);
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\UserBundle\Application\Exception\IdentityException
	 */
	protected function startup(): void
	{
		parent::startup();

		$userView = $this->getIdentity()->data();
		assert($userView instanceof UserView);

		ApplicationDateTimeZone::set($userView->timezone);
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

		$userView = $this->getIdentity()->data();
		assert($userView instanceof UserView);

		$template->identity = $userView;
		$template->locales = $this->validLocalesProvider->getValidLocales();
		$template->defaultLocale = $this->validLocalesProvider->getValidDefaultLocale();
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

	protected function redrawSidebar(): void
	{
		$this->redrawControl('sidebar-menu-mobile');
		$this->redrawControl('sidebar-menu-desktop');
	}

	/**
	 * @return \Contributte\MenuControl\UI\MenuComponent
	 */
	protected function createComponentSidebarMenu(): MenuComponent
	{
		$control = $this->menuComponentFactory->create(self::MENU_NAME_SIDEBAR);

		$control->onAnchor[] = function (MenuComponent $component) {
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

	/**
	 * @return \App\Web\Control\Footer\FooterControl
	 */
	protected function createComponentFooter(): FooterControl
	{
		return $this->footerControlFactory->create();
	}
}
