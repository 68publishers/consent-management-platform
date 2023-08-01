<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerList;

interface ScenarioSchedulerListControlFactoryInterface
{
    public function create(): ScenarioSchedulerListControl;
}
