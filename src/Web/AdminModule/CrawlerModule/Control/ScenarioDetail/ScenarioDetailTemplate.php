<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ResponseBody\ScenarioResponseBody;

final class ScenarioDetailTemplate extends Template
{
    public ScenarioResponseBody $scenarioResponseBody;

    public string $serializedScenarioConfig;
}
