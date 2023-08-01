<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulerResponse;
use Throwable;

final class DeleteScenarioSchedulerModalTemplate extends Template
{
    public ?string $scenarioSchedulerId = null;

    public ?ScenarioSchedulerResponse $scenarioSchedulerResponse = null;

    public ?Throwable $responseError = null;
}
