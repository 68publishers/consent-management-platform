<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

interface ScenarioDetailModalControlFactoryInterface
{
    public function create(string $scenarioId): ScenarioDetailModalControl;
}
