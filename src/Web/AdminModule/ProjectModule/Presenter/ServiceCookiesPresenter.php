<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectCookieResource;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;

#[Allowed(resource: ProjectCookieResource::class, privilege: ProjectCookieResource::READ)]
final class ServiceCookiesPresenter extends SelectedProjectPresenter
{
	private CookieListControlFactoryInterface $cookieListControlFactory;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface $cookieListControlFactory
	 */
	public function __construct(CookieListControlFactoryInterface $cookieListControlFactory)
	{
		parent::__construct();

		$this->cookieListControlFactory = $cookieListControlFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function startup(): void
	{
		parent::startup();

		$this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl
	 */
	protected function createComponentList(): CookieListControl
	{
		$control = $this->cookieListControlFactory->create(
			$this->validLocalesProvider->withLocalesConfig($this->projectView->locales)
		);

		$control->configureActions(FALSE, FALSE);
		$control->projectOnly($this->projectView->id, TRUE);

		return $control;
	}
}
