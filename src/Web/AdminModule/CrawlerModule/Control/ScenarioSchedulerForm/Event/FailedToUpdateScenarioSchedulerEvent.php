<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class FailedToUpdateScenarioSchedulerEvent extends Event
{
    public function __construct(
        private readonly Throwable $error,
    ) {}

    public function getError(): Throwable
    {
        return $this->error;
    }
}
