<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\FrontModule\Control\SignIn\Event\AuthenticationFailedEvent;
use App\Web\FrontModule\Control\SignIn\Event\LoggedInEvent;
use App\Web\FrontModule\Control\SignIn\SignInControl;
use App\Web\FrontModule\Control\SignIn\SignInControlFactoryInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;

final class SignInPresenter extends FrontPresenter
{
    /** @persistent */
    public string $backLink = '';

    private SignInControlFactoryInterface $signInControlFactory;

    public function __construct(SignInControlFactoryInterface $signInControlFactory)
    {
        parent::__construct();

        $this->signInControlFactory = $signInControlFactory;
    }

    protected function createComponentSignIn(): SignInControl
    {
        $control = $this->signInControlFactory->create();

        $control->addEventListener(LoggedInEvent::class, function (): void {
            if (!empty($this->backLink)) {
                $this->restoreRequest($this->backLink);
            }

            $this->redirect(':Admin:Dashboard:');
        });

        $control->addEventListener(AuthenticationFailedEvent::class, function (): void {
            $this->subscribeFlashMessage(FlashMessage::error('user_authentication_failed'));
        });

        return $control;
    }
}
