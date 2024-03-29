<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\FrontModule\Control\ResetPassword\Event\PasswordRequestExpiredEvent;
use App\Web\FrontModule\Control\ResetPassword\Event\PasswordResetEvent;
use App\Web\FrontModule\Control\ResetPassword\Event\PasswordResetFailedEvent;
use App\Web\FrontModule\Control\ResetPassword\ResetPasswordControl;
use App\Web\FrontModule\Control\ResetPassword\ResetPasswordControlFactoryInterface;
use Nette\Application\AbortException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\ValueObject\PasswordRequestId;
use SixtyEightPublishers\ForgotPasswordBundle\ReadModel\Query\GetPasswordRequestByIdQuery;
use SixtyEightPublishers\ForgotPasswordBundle\ReadModel\View\PasswordRequestView;

final class ResetPasswordPresenter extends FrontPresenter
{
    public function __construct(
        private readonly ResetPasswordControlFactoryInterface $resetPasswordControlFactory,
        private readonly QueryBusInterface $queryBus,
    ) {
        parent::__construct();
    }

    /**
     * @throws AbortException
     */
    public function actionDefault(string $id): void
    {
        $passwordRequestView = PasswordRequestId::isValid($id) ? $this->queryBus->dispatch(GetPasswordRequestByIdQuery::create($id)) : null;

        if (!$passwordRequestView instanceof PasswordRequestView || $passwordRequestView->expired() || $passwordRequestView->status->isFinished()) {
            $this->subscribeFlashMessage(FlashMessage::info('password_request_expired'));
            $this->redirect('SignIn:');
        }
    }

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
