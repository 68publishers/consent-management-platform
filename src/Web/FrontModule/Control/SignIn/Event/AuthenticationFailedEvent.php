<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn\Event;

use SixtyEightPublishers\UserBundle\Application\Exception\AuthenticationException;
use Symfony\Contracts\EventDispatcher\Event;

final class AuthenticationFailedEvent extends Event
{
    public function __construct(
        private readonly AuthenticationException $exception,
    ) {}

    public function exception(): AuthenticationException
    {
        return $this->exception;
    }
}
