<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\AbortScenario;

use App\Application\Crawler\CrawlerClientProvider;
use App\Web\AdminModule\CrawlerModule\Control\AbortScenario\Event\FailedToAbortScenarioEvent;
use App\Web\AdminModule\CrawlerModule\Control\AbortScenario\Event\ScenarioAbortedEvent;
use App\Web\Ui\Modal\AbstractModalControl;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenarioResponse;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenariosController;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;
use Throwable;

final class AbortScenarioModalControl extends AbstractModalControl
{
    private string $scenarioId;

    private CrawlerClientProvider $crawlerClientProvider;

    private ?ScenarioResponse $scenarioResponse = null;

    private ?Throwable $responseError = null;

    public function __construct(
        string $scenarioId,
        CrawlerClientProvider $crawlerClientProvider,
    ) {
        $this->scenarioId = $scenarioId;
        $this->crawlerClientProvider = $crawlerClientProvider;
    }

    public function handleAbort(): void
    {
        try {
            $controller = $this->crawlerClientProvider->get()->getController(ScenariosController::class);
            $controller->abortScenario($this->scenarioId);
        } catch (Throwable $e) {
            $this->responseError = $e;
            $this->dispatchEvent(new FailedToAbortScenarioEvent($e));
            $this->redrawControl();

            return;
        }

        $this->dispatchEvent(new ScenarioAbortedEvent());
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof AbortScenarioModalTemplate);

        $template->scenarioId = $this->scenarioId;
        $template->scenarioResponse = null === $this->responseError ? $this->getScenarioResponse() : null;
        $template->responseError = $this->responseError;
    }

    private function getScenarioResponse(): ?ScenarioResponse
    {
        if (null !== $this->scenarioResponse || null !== $this->responseError) {
            return $this->scenarioResponse;
        }

        try {
            $client = $this->crawlerClientProvider->get();
            $this->scenarioResponse = $client
                ->getController(ScenariosController::class)
                ->getScenario($this->scenarioId);
        } catch (ControllerResponseExceptionInterface $e) {
            $this->scenarioResponse = null;
            $this->responseError = $e;
        } catch (Throwable $e) {
            $this->logger->error((string) $e);

            $this->scenarioResponse = null;
            $this->responseError = $e;
        }

        return $this->scenarioResponse;
    }
}
