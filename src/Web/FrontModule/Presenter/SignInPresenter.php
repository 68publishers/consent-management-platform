<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\FrontModule\Control\SignIn\SignInControl;
use App\Web\FrontModule\Control\SignIn\Event\LoggedInEvent;
use App\Web\FrontModule\Control\SignIn\SignInControlFactoryInterface;
use App\Web\FrontModule\Control\SignIn\Event\AuthenticationFailedEvent;

final class SignInPresenter extends FrontPresenter
{
	/** @persistent */
	public string $backLink = '';

	private SignInControlFactoryInterface $signInControlFactory;

	/**
	 * @param \App\Web\FrontModule\Control\SignIn\SignInControlFactoryInterface $signInControlFactory
	 */
	public function __construct(SignInControlFactoryInterface $signInControlFactory)
	{
		parent::__construct();

		$this->signInControlFactory = $signInControlFactory;
	}

	/**
	 * @return \App\Web\FrontModule\Control\SignIn\SignInControl
	 */
	protected function createComponentSignIn(): SignInControl
	{
		$control = $this->signInControlFactory->create();

		$control->addEventListener(LoggedInEvent::class, function () {
			if (!empty($this->backLink)) {
				$this->restoreRequest($this->backLink);
			}

			$this->redirect(':Admin:Dashboard:');
		});

		$control->addEventListener(AuthenticationFailedEvent::class, function (AuthenticationFailedEvent $event) {
			bdump($event->getException());
		});

		return $control;
	}
}
