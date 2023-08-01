<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class LocalizationSettingsUpdateFailedEvent extends Event
{
    public function __construct(
        private readonly Throwable $error,
    ) {}

    public function getError(): Throwable
    {
        return $this->error;
    }
}
