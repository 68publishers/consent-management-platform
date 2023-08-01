<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm;

use App\Application\Crawler\CrawlerClientProvider;
use App\Application\Crawler\CrawlerNotConfiguredException;
use App\Web\AdminModule\CrawlerModule\Control\GetScenarioSchedulerResponseTrait;
use App\Web\Ui\Modal\AbstractModalControl;
use Closure;
use Nette\Application\BadRequestException;

final class ScenarioSchedulerFormModalControl extends AbstractModalControl
{
    use GetScenarioSchedulerResponseTrait;

    private ?Closure $innerControlCreationCallback = null;

    public function __construct(
        private readonly CrawlerClientProvider $crawlerClientProvider,
        private readonly ScenarioSchedulerFormControlFactoryInterface $runScenarioFormControlFactory,
        private readonly ?string $scenarioSchedulerId,
    ) {}

    public function setInnerControlCreationCallback(?Closure $innerControlCreationCallback): void
    {
        $this->innerControlCreationCallback = $innerControlCreationCallback;
    }

    /**
     * @throws CrawlerNotConfiguredException
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ScenarioSchedulerFormModalTemplate);

        $template->scenarioSchedulerId = $this->scenarioSchedulerId;
        $template->scenarioSchedulerResponse = $this->getScenarioSchedulerResponse($this->crawlerClientProvider->get(), $this->scenarioSchedulerId);
        $template->responseError = $this->responseError;
    }

    /**
     * @throws BadRequestException
     * @throws CrawlerNotConfiguredException
     */
    protected function createComponentScenarioSchedulerForm(): ScenarioSchedulerFormControl
    {
        $response = null;

        if (null !== $this->scenarioSchedulerId) {
            $response = $this->getScenarioSchedulerResponse($this->crawlerClientProvider->get(), $this->scenarioSchedulerId);

            if (null === $response) {
                $this->error(sprintf(
                    'Unable to fetch response for scenario scheduler %s. %s',
                    $this->scenarioSchedulerId,
                    null !== $this->responseError ? (string) $this->responseError : '',
                ));
            }
        }

        $control = $this->runScenarioFormControlFactory->create($response);

        if (null !== $this->innerControlCreationCallback) {
            ($this->innerControlCreationCallback)($control);
        }

        return $control;
    }
}
