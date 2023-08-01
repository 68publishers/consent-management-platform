<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event;

use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use Symfony\Contracts\EventDispatcher\Event;

final class BasicInformationUpdatedEvent extends Event
{
    private UserId $userId;

    private string $oldProfile;

    private string $newProfile;

    public function __construct(UserId $userId, string $oldProfile, string $newProfile)
    {
        $this->userId = $userId;
        $this->oldProfile = $oldProfile;
        $this->newProfile = $newProfile;
    }

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
