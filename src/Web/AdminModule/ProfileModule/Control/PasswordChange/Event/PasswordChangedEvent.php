<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\PasswordChange\Event;

use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use Symfony\Contracts\EventDispatcher\Event;

final class PasswordChangedEvent extends Event
{
    public function __construct(
        private readonly UserId $userId,
    ) {}

    public function userId(): UserId
    {
        return $this->userId;
    }
}
