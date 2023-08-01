<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event;

use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use Symfony\Contracts\EventDispatcher\Event;

final class NotificationPreferencesUpdatedEvent extends Event
{
    public function __construct(
        private readonly UserId $userId,
    ) {}

    public function userId(): UserId
    {
        return $this->userId;
    }
}
