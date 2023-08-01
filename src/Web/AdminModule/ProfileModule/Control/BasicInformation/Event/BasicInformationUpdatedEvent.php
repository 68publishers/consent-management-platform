<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event;

use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use Symfony\Contracts\EventDispatcher\Event;

final class BasicInformationUpdatedEvent extends Event
{
    public function __construct(
        private readonly UserId $userId,
        private readonly string $oldProfile,
        private readonly string $newProfile,
    ) {}

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function oldProfile(): string
    {
        return $this->oldProfile;
    }

    public function newProfile(): string
    {
        return $this->newProfile;
    }
}
