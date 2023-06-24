<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm;

interface ScenarioSchedulerFormModalControlFactoryInterface
{
	public function create(?string $scenarioSchedulerId = NULL): ScenarioSchedulerFormModalControl;
}
