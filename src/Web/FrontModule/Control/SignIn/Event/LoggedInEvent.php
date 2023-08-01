<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn\Event;

use App\ReadModel\User\UserView;
use Symfony\Contracts\EventDispatcher\Event;

final class LoggedInEvent extends Event
{
    private UserView $userView;

    public function __construct(UserView $userView)
    {
        $this->userView = $userView;
    }

    public function userView(): UserView
    {
        return $this->userView;
    }
}
