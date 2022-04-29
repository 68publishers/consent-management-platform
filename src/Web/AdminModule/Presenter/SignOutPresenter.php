<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use SixtyEightPublishers\UserBundle\Bridge\Nette\Ui\LogoutPresenterTrait;

final class SignOutPresenter extends AdminPresenter
{
	use LogoutPresenterTrait;

	/**
	 * {@inheritDoc}
	 */
	protected function handleUserLoggedOut(): void
	{
		$this->redirect(':Front:SignIn:');
	}
}
