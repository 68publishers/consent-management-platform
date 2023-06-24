<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm;

interface RunScenarioFormControlFactoryInterface
{
	public function create(): RunScenarioFormControl;
}
