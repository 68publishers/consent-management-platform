<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm;

use App\Web\Ui\Modal\AbstractModalControl;

final class RunScenarioFormModalControl extends AbstractModalControl
{
	private RunScenarioFormControlFactoryInterface $runScenarioFormControlFactory;

	public function __construct(RunScenarioFormControlFactoryInterface $runScenarioFormControlFactory)
	{
		$this->runScenarioFormControlFactory = $runScenarioFormControlFactory;
	}

	public function getInnerControl(): RunScenarioFormControl
	{
		return $this->getComponent('runScenarioForm');
	}

	protected function createComponentRunScenarioForm(): RunScenarioFormControl
	{
		return $this->runScenarioFormControlFactory->create();
	}
}
