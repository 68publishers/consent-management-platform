<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm;

use App\Web\Ui\Modal\AbstractModalControl;
use Closure;

final class RunScenarioFormModalControl extends AbstractModalControl
{
    private ?Closure $innerControlCreationCallback = null;

    public function __construct(
        private readonly RunScenarioFormControlFactoryInterface $runScenarioFormControlFactory,
    ) {}

    public function setInnerControlCreationCallback(?Closure $innerControlCreationCallback): void
    {
        $this->innerControlCreationCallback = $innerControlCreationCallback;
    }

    protected function createComponentRunScenarioForm(): RunScenarioFormControl
    {
        $control = $this->runScenarioFormControlFactory->create();

        if (null !== $this->innerControlCreationCallback) {
            ($this->innerControlCreationCallback)($control);
        }

        return $control;
    }
}
