<?php

declare(strict_types=1);

namespace App\Web\Control\Localization\Event;

use App\Application\Localization\Profile;
use Symfony\Contracts\EventDispatcher\Event;

final class ProfileChangedEvent extends Event
{
    private Profile $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function profile(): Profile
    {
        return $this->profile;
    }
}
