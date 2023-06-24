<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioList;

interface ScenarioListControlFactoryInterface
{
	public function create(): ScenarioListControl;
}
