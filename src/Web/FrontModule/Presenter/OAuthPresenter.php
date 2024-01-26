<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use Psr\Log\LoggerInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\OAuth\Bridge\Nette\Application\OAuthPresenterTrait;
use SixtyEightPublishers\OAuth\Exception\OAuthExceptionInterface;

final class OAuthPresenter extends FrontPresenter
{
    use OAuthPresenterTrait;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function onAuthorizationRedirectFailed(string $flowName, OAuthExceptionInterface $error): never
    {
        $this->logger->error($error->getMessage(), [
            'exception' => $error,
        ]);

        $this->subscribeFlashMessage(FlashMessage::error('authentication_failure.' . $flowName));
        $this->redirect(':Front:SignIn:');
    }

    protected function onAuthenticationFailed(string $flowName, OAuthExceptionInterface $error): never
    {
        $this->subscribeFlashMessage(FlashMessage::error('authentication_failure.' . $flowName));
        $this->redirect(':Front:SignIn:');
    }

    protected function onUserAuthenticated(string $flowName): never
    {
        $this->redirect(':Admin:Dashboard:');
    }
}
