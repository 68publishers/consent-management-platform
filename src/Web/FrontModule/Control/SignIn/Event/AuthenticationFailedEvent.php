<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn\Event;

use SixtyEightPublishers\UserBundle\Application\Exception\AuthenticationException;
use Symfony\Contracts\EventDispatcher\Event;

final class AuthenticationFailedEvent extends Event
{
    private AuthenticationException $exception;

    public function __construct(AuthenticationException $exception)
    {
        $this->exception = $exception;
    }

    public function exception(): AuthenticationException
    {
        return $this->exception;
    }
}
