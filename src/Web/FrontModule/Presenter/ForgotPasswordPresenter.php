<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use SixtyEightPublishers\FlashMessageBundle\Domain\Phrase;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\FrontModule\Control\ForgotPassword\ForgotPasswordControl;
use App\Web\FrontModule\Control\ForgotPassword\Event\EmailAddressNotFoundEvent;
use App\Web\FrontModule\Control\ForgotPassword\Event\PasswordChangeRequestedEvent;
use App\Web\FrontModule\Control\ForgotPassword\ForgotPasswordControlFactoryInterface;

final class ForgotPasswordPresenter extends FrontPresenter
{
	private ForgotPasswordControlFactoryInterface $forgotPasswordControlFactory;

	/**
	 * @param \App\Web\FrontModule\Control\ForgotPassword\ForgotPasswordControlFactoryInterface $forgotPasswordControlFactory
	 */
	public function __construct(ForgotPasswordControlFactoryInterface $forgotPasswordControlFactory)
	{
		parent::__construct();

		$this->forgotPasswordControlFactory = $forgotPasswordControlFactory;
	}

	/**
	 * @return \App\Web\FrontModule\Control\ForgotPassword\ForgotPasswordControl
	 */
	protected function createComponentForgotPassword(): ForgotPasswordControl
	{
		$control = $this->forgotPasswordControlFactory->create();

		$control->addEventListener(PasswordChangeRequestedEvent::class, function (PasswordChangeRequestedEvent $event): void {
			$this->subscribeFlashMessage(FlashMessage::success(Phrase::create('password_change_requested', ['email_address' => $event->emailAddress()])));
			$this->redirect('this');
		});

		$control->addEventListener(EmailAddressNotFoundEvent::class, function (EmailAddressNotFoundEvent $event): void {
			$this->subscribeFlashMessage(FlashMessage::error(Phrase::create('email_address_not_found', ['email_address' => $event->emailAddress()])));
		});

		return $control;
	}
}
