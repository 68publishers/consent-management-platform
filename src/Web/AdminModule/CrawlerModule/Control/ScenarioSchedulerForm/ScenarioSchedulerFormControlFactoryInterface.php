<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm;

use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulerResponse;

interface ScenarioSchedulerFormControlFactoryInterface
{
    public function create(?ScenarioSchedulerResponse $scenarioSchedulerResponse = null): ScenarioSchedulerFormControl;
}
