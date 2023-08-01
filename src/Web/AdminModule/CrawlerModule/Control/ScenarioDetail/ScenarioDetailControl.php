<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

use App\Web\Ui\Control;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ResponseBody\ScenarioResponseBody;

final class ScenarioDetailControl extends Control
{
    private ScenarioResponseBody $scenarioResponseBody;

    private string $serializedScenarioConfig;

    public function __construct(ScenarioResponseBody $scenarioResponseBody, string $serializedScenarioConfig)
    {
        $this->scenarioResponseBody = $scenarioResponseBody;
        $this->serializedScenarioConfig = $serializedScenarioConfig;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ScenarioDetailTemplate);

        $template->scenarioResponseBody = $this->scenarioResponseBody;
        $template->serializedScenarioConfig = $this->serializedScenarioConfig;
    }
}
