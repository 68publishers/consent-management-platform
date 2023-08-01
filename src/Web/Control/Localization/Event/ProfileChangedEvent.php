<?php

declare(strict_types=1);

namespace App\Web\Control\Localization\Event;

use App\Application\Localization\Profile;
use Symfony\Contracts\EventDispatcher\Event;

final class ProfileChangedEvent extends Event
{
    public function __construct(
        private readonly Profile $profile,
    ) {}

    public function profile(): Profile
    {
        return $this->profile;
    }
}
