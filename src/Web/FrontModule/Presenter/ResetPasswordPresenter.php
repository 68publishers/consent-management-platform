<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Web\FrontModule\Control\ResetPassword\ResetPasswordControl;
use App\Web\FrontModule\Control\ResetPassword\Event\PasswordResetEvent;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Dto\PasswordRequestId;
use App\Web\FrontModule\Control\ResetPassword\Event\PasswordResetFailedEvent;
use App\Web\FrontModule\Control\ResetPassword\Event\PasswordRequestExpiredEvent;
use SixtyEightPublishers\ForgotPasswordBundle\ReadModel\View\PasswordRequestView;
use App\Web\FrontModule\Control\ResetPassword\ResetPasswordControlFactoryInterface;
use SixtyEightPublishers\ForgotPasswordBundle\ReadModel\Query\GetPasswordRequestByIdQuery;
use SixtyEightPublishers\ArchitectureBundle\Domain\Exception\InvalidIdentityValueException;

final class ResetPasswordPresenter extends FrontPresenter
{
	private ResetPasswordControlFactoryInterface $resetPasswordControlFactory;

	private QueryBusInterface $queryBus;

	/**
	 * @param \App\Web\FrontModule\Control\ResetPassword\ResetPasswordControlFactoryInterface $resetPasswordControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                  $queryBus
	 */
	public function __construct(ResetPasswordControlFactoryInterface $resetPasswordControlFactory, QueryBusInterface $queryBus)
	{
		parent::__construct();

		$this->resetPasswordControlFactory = $resetPasswordControlFactory;
		$this->queryBus = $queryBus;
	}

	/**
	 * @param string $id
	 *
	 * @return void
	 * @throws \Nette\Application\AbortException
	 */
	public function actionDefault(string $id): void
	{
		try {
			$passwordRequestView = $this->queryBus->dispatch(GetPasswordRequestByIdQuery::create(PasswordRequestId::fromString($id)->toString()));
		} catch (InvalidIdentityValueException $e) {
		}

		if (!isset($passwordRequestView) || !$passwordRequestView instanceof PasswordRequestView || $passwordRequestView->expired() || $passwordRequestView->status->isFinished()) {
			$this->subscribeFlashMessage(FlashMessage::info('password_request_expired'));
			$this->redirect('SignIn:');
		}
	}

	/**
	 * @return \App\Web\FrontModule\Control\ResetPassword\ResetPasswordControl
	 */
	protected function createComponentResetPassword(): ResetPasswordControl
	{
		$control = $this->resetPasswordControlFactory->create(PasswordRequestId::fromString($this->getParameter('id')));

		$control->addEventListener(PasswordResetEvent::class, function (): void {
			$this->subscribeFlashMessage(FlashMessage::success('password_reset'));
			$this->redirect('SignIn:');
		});

		$control->addEventListener(PasswordRequestExpiredEvent::class, function (): void {
			$this->subscribeFlashMessage(FlashMessage::info('password_request_expired'));
			$this->redirect('SignIn:');
		});

		$control->addEventListener(PasswordResetFailedEvent::class, function (): void {
			$this->subscribeFlashMessage(FlashMessage::error('password_reset_failed'));
			$this->redirect('this');
		});

		return $control;
	}
}
