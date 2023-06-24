<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\AbortScenario;

use Throwable;
use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenarioResponse;

final class AbortScenarioModalTemplate extends Template
{
	public ?string $scenarioId = NULL;

	public ?ScenarioResponse $scenarioResponse = NULL;

	public ?Throwable $responseError = NULL;
}
