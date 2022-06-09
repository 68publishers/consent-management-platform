<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\ReadModel\User\UserView;
use SixtyEightPublishers\UserBundle\Bridge\Nette\Ui\LogoutPresenterTrait;

final class SignOutPresenter extends AdminPresenter
{
	use LogoutPresenterTrait;

	/**
	 * {@inheritDoc}
	 *
	 * @throws \SixtyEightPublishers\UserBundle\Application\Exception\IdentityException
	 */
	protected function handleUserLoggedOut(): void
	{
		$userView = $this->getIdentity()->data();
		assert($userView instanceof UserView);

		$this->redirect(':Front:SignIn:', [
			'locale' => $userView->profileLocale->value(),
		]);
	}
}
