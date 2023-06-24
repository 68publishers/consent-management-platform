<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\AbortScenario;

interface AbortScenarioModalControlFactoryInterface
{
	public function create(string $scenarioId): AbortScenarioModalControl;
}
