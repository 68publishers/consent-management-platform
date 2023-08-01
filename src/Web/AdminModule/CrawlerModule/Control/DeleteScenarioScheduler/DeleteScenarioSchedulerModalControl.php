<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler;

use App\Application\Crawler\CrawlerClientProvider;
use App\Application\Crawler\CrawlerNotConfiguredException;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\Event\FailedToDeleteScenarioSchedulerEvent;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\Event\ScenarioSchedulerDeletedEvent;
use App\Web\AdminModule\CrawlerModule\Control\GetScenarioSchedulerResponseTrait;
use App\Web\Ui\Modal\AbstractModalControl;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulersController;
use Throwable;

final class DeleteScenarioSchedulerModalControl extends AbstractModalControl
{
    use GetScenarioSchedulerResponseTrait;

    public function __construct(
        private readonly string $scenarioSchedulerId,
        private readonly CrawlerClientProvider $crawlerClientProvider,
    ) {}

    public function handleDelete(): void
    {
        try {
            $controller = $this->crawlerClientProvider->get()->getController(ScenarioSchedulersController::class);
            $controller->deleteScenarioScheduler($this->scenarioSchedulerId);
        } catch (Throwable $e) {
            $this->responseError = $e;
            $this->dispatchEvent(new FailedToDeleteScenarioSchedulerEvent($e));
            $this->redrawControl();

            return;
        }

        $this->dispatchEvent(new ScenarioSchedulerDeletedEvent());
    }

    /**
     * @throws CrawlerNotConfiguredException
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof DeleteScenarioSchedulerModalTemplate);

        $template->scenarioSchedulerId = $this->scenarioSchedulerId;
        $template->scenarioSchedulerResponse = null === $this->responseError ? $this->getScenarioSchedulerResponse($this->crawlerClientProvider->get(), $this->scenarioSchedulerId) : null;
        $template->responseError = $this->responseError;
    }
}
