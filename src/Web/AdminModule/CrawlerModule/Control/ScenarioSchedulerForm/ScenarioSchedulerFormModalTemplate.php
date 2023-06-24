<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm;

use Throwable;
use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulerResponse;

final class ScenarioSchedulerFormModalTemplate extends Template
{
	public ?string $scenarioSchedulerId = NULL;

	public ?ScenarioSchedulerResponse $scenarioSchedulerResponse = NULL;

	public ?Throwable $responseError = NULL;
}
