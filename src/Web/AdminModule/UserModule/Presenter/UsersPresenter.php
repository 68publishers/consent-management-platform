<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\UserModule\Control\UserList\UserListControl;
use App\Web\AdminModule\UserModule\Control\UserList\UserListControlFactoryInterface;

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
	 * @return void
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
