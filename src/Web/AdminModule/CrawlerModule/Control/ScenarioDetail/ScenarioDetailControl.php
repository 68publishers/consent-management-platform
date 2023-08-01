<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

use App\Web\Ui\Control;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ResponseBody\ScenarioResponseBody;

final class ScenarioDetailControl extends Control
{
    public function __construct(
        private readonly ScenarioResponseBody $scenarioResponseBody,
        private readonly string $serializedScenarioConfig,
    ) {}

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ScenarioDetailTemplate);

        $template->scenarioResponseBody = $this->scenarioResponseBody;
        $template->serializedScenarioConfig = $this->serializedScenarioConfig;
    }
}
