<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn\Event;

use App\ReadModel\User\UserView;
use Symfony\Contracts\EventDispatcher\Event;

final class LoggedInEvent extends Event
{
    public function __construct(
        private readonly UserView $userView,
    ) {}

    public function userView(): UserView
    {
        return $this->userView;
    }
}
