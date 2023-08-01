<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulerResponse;
use Throwable;

final class ScenarioSchedulerFormModalTemplate extends Template
{
    public ?string $scenarioSchedulerId = null;

    public ?ScenarioSchedulerResponse $scenarioSchedulerResponse = null;

    public ?Throwable $responseError = null;
}
