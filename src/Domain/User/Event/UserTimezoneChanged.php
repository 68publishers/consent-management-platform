<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use DateTimeZone;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class UserTimezoneChanged extends AbstractDomainEvent
{
    private UserId $userId;

    private DateTimeZone $timezone;

    public static function create(UserId $userId, DateTimeZone $timezone): self
    {
        $event = self::occur($userId->toString(), [
            'timezone' => $timezone->getName(),
        ]);

        $event->userId = $userId;
        $event->timezone = $timezone;

        return $event;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function timezone(): DateTimeZone
    {
        return $this->timezone;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->userId = UserId::fromUuid($this->aggregateId()->id());
        $this->timezone = new DateTimeZone($parameters['timezone']);
    }
}
