<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm;

use Closure;
use App\Web\Ui\Modal\AbstractModalControl;

final class RunScenarioFormModalControl extends AbstractModalControl
{
	private RunScenarioFormControlFactoryInterface $runScenarioFormControlFactory;

	private ?Closure $innerControlCreationCallback = NULL;

	public function __construct(RunScenarioFormControlFactoryInterface $runScenarioFormControlFactory)
	{
		$this->runScenarioFormControlFactory = $runScenarioFormControlFactory;
	}

	public function setInnerControlCreationCallback(?Closure $innerControlCreationCallback): void
	{
		$this->innerControlCreationCallback = $innerControlCreationCallback;
	}

	protected function createComponentRunScenarioForm(): RunScenarioFormControl
	{
		$control = $this->runScenarioFormControlFactory->create();

		if (NULL !== $this->innerControlCreationCallback) {
			($this->innerControlCreationCallback)($control);
		}

		return $control;
	}
}
