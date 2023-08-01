<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\Application\Acl\UserResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use App\Web\AdminModule\UserModule\Control\UserList\UserListControl;
use App\Web\AdminModule\UserModule\Control\UserList\UserListControlFactoryInterface;

#[Allowed(resource: UserResource::class, privilege: UserResource::READ)]
final class UsersPresenter extends AdminPresenter
{
	private UserListControlFactoryInterface $userListControlFactory;

	/**
	 * @param \App\Web\AdminModule\UserModule\Control\UserList\UserListControlFactoryInterface $userListControlFactory
	 */
	public function __construct(UserListControlFactoryInterface $userListControlFactory)
	{
		parent::__construct();

		$this->userListControlFactory = $userListControlFactory;
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
	 * @return \App\Web\AdminModule\UserModule\Control\UserList\UserListControl
	 */
	protected function createComponentList(): UserListControl
	{
		return $this->userListControlFactory->create();
	}
}
