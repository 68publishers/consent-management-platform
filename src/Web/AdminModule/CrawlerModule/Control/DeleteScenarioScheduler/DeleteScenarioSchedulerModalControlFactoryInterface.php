<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler;

interface DeleteScenarioSchedulerModalControlFactoryInterface
{
    public function create(string $scenarioSchedulerId): DeleteScenarioSchedulerModalControl;
}
