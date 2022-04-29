<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\Web\Ui\Presenter;
use Contributte\MenuControl\UI\MenuComponent;
use Contributte\MenuControl\UI\IMenuComponentFactory;
use SixtyEightPublishers\SmartNetteComponent\Annotation\LoggedIn;
use SixtyEightPublishers\UserBundle\Bridge\Nette\Security\Identity;
use SixtyEightPublishers\UserBundle\Application\Csrf\CsrfTokenFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\AuthorizationAnnotationInterface;

/**
 * @LoggedIn()
 */
abstract class AdminPresenter extends Presenter
{
	private const MENU_NAME_SIDEBAR = 'sidebar';

	private CsrfTokenFactoryInterface $csrfTokenFactory;

	private IMenuComponentFactory $menuComponentFactory;

	/**
	 * @param \SixtyEightPublishers\UserBundle\Application\Csrf\CsrfTokenFactoryInterface $csrfTokenFactory
	 * @param \Contributte\MenuControl\UI\IMenuComponentFactory                           $menuComponentFactory
	 *
	 * @return void
	 */
	public function injectAdminDependencies(CsrfTokenFactoryInterface $csrfTokenFactory, IMenuComponentFactory $menuComponentFactory): void
	{
		$this->csrfTokenFactory = $csrfTokenFactory;
		$this->menuComponentFactory = $menuComponentFactory;
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
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		assert($template instanceof AdminTemplate);

		$identity = $this->getUser()->getIdentity();
		assert($identity instanceof Identity);

		$template->identity = $identity->data();
		$template->signOutLink = $this->link(':Admin:SignOut:', [
			'_sec' => $this->csrfTokenFactory->create(SignOutPresenter::class),
		]);
	}

	/**
	 * @return \Contributte\MenuControl\UI\MenuComponent
	 */
	protected function createComponentSidebarMenu(): MenuComponent
	{
		return $this->menuComponentFactory->create(self::MENU_NAME_SIDEBAR);
	}
}
