<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ResponseBody\ScenarioResponseBody;

interface ScenarioDetailControlFactoryInterface
{
    public function create(ScenarioResponseBody $scenarioResponseBody, string $serializedScenarioConfig): ScenarioDetailControl;
}
