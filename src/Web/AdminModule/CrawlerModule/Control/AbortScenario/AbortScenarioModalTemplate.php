<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\AbortScenario;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenarioResponse;
use Throwable;

final class AbortScenarioModalTemplate extends Template
{
    public ?string $scenarioId = null;

    public ?ScenarioResponse $scenarioResponse = null;

    public ?Throwable $responseError = null;
}
