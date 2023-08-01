<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\ApiCacheSettingsForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class ApiCacheSettingsUpdateFailedEvent extends Event
{
    private Throwable $error;

    public function __construct(Throwable $error)
    {
        $this->error = $error;
    }

    public function getError(): Throwable
    {
        return $this->error;
    }
}
