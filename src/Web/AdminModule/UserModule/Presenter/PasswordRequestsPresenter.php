<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControl;
use App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControlFactoryInterface;

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
	 * @return \App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControl
	 */
	protected function createComponentList(): PasswordRequestListControl
	{
		return $this->passwordRequestListControlFactory->create();
	}
}
