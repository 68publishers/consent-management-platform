<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\User\ValueObject\NotificationPreferences;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class UserNotificationPreferencesChanged extends AbstractDomainEvent
{
    private UserId $userId;

    private NotificationPreferences $notificationPreferences;

    public static function create(UserId $userId, NotificationPreferences $notificationPreferences): self
    {
        $event = self::occur($userId->toString(), [
            'notification_preferences' => $notificationPreferences,
        ]);

        $event->userId = $userId;
        $event->notificationPreferences = $notificationPreferences;

        return $event;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function notificationPreferences(): NotificationPreferences
    {
        return $this->notificationPreferences;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->userId = UserId::fromUuid($this->aggregateId()->id());
        $this->notificationPreferences = NotificationPreferences::reconstitute($parameters['notification_preferences']);
    }
}
