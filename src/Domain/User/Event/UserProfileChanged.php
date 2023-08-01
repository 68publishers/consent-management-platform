<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Shared\ValueObject\Locale;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class UserProfileChanged extends AbstractDomainEvent
{
    private UserId $userId;

    private Locale $profileLocale;

    public static function create(UserId $userId, Locale $profileLocale): self
    {
        $event = self::occur($userId->toString(), [
            'profile_locale' => $profileLocale->value(),
        ]);

        $event->userId = $userId;
        $event->profileLocale = $profileLocale;

        return $event;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function profileLocale(): Locale
    {
        return $this->profileLocale;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->userId = UserId::fromUuid($this->aggregateId()->id());
        $this->profileLocale = Locale::fromValue($parameters['profile_locale']);
    }
}
