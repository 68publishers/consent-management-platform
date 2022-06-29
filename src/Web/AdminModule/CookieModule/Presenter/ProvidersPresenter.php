<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CookieProviderResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControl;
use App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControlFactoryInterface;

/**
 * @IsAllowed(resource=CookieProviderResource::class, privilege=CookieProviderResource::READ)
 */
final class ProvidersPresenter extends AdminPresenter
{
	private ProviderListControlFactoryInterface $providerListControlFactory;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControlFactoryInterface $providerListControlFactory
	 */
	public function __construct(ProviderListControlFactoryInterface $providerListControlFactory)
	{
		parent::__construct();

		$this->providerListControlFactory = $providerListControlFactory;
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
	 * @return \App\Web\AdminModule\CookieModule\Control\ProviderList\ProviderListControl
	 */
	protected function createComponentList(): ProviderListControl
	{
		return $this->providerListControlFactory->create();
	}
}
