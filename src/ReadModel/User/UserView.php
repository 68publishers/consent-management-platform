<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\User\ValueObject\NotificationPreferences;
use DateTimeZone;
use SixtyEightPublishers\UserBundle\ReadModel\View\UserView as BaseUserView;

final class UserView extends BaseUserView
{
    public Locale $profileLocale;

    public DateTimeZone $timezone;

    public NotificationPreferences $notificationPreferences;

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['profile'] = $this->profileLocale->value();
        $data['timezone'] = $this->timezone->getName();
        $data['notificationPreferences'] = $this->notificationPreferences->toArray();

        return $data;
    }
}
