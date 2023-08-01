<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\Application\Acl\PasswordRequestResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControl;
use App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControlFactoryInterface;

#[Allowed(resource: PasswordRequestResource::class, privilege: PasswordRequestResource::READ)]
final class PasswordRequestsPresenter extends AdminPresenter
{
	private PasswordRequestListControlFactoryInterface $passwordRequestListControlFactory;

	/**
	 * @param \App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControlFactoryInterface $passwordRequestListControlFactory
	 */
	public function __construct(PasswordRequestListControlFactoryInterface $passwordRequestListControlFactory)
	{
		parent::__construct();

		$this->passwordRequestListControlFactory = $passwordRequestListControlFactory;
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
	 * @return \App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControl
	 */
	protected function createComponentList(): PasswordRequestListControl
	{
		return $this->passwordRequestListControlFactory->create();
	}
}
