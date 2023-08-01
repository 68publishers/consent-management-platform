<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control;

use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulerResponse;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulersController;
use SixtyEightPublishers\CrawlerClient\CrawlerClientInterface;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;
use Throwable;

trait GetScenarioSchedulerResponseTrait
{
    protected ?ScenarioSchedulerResponse $scenarioSchedulerResponse = null;

    protected ?Throwable $responseError = null;

    protected function getScenarioSchedulerResponse(CrawlerClientInterface $client, ?string $scenarioSchedulerId): ?ScenarioSchedulerResponse
    {
        if (null === $scenarioSchedulerId || null !== $this->scenarioSchedulerResponse || null !== $this->responseError) {
            return $this->scenarioSchedulerResponse;
        }

        try {
            $this->scenarioSchedulerResponse = $client
                ->getController(ScenarioSchedulersController::class)
                ->getScenarioScheduler($scenarioSchedulerId);
        } catch (ControllerResponseExceptionInterface $e) {
            $this->scenarioSchedulerResponse = null;
            $this->responseError = $e;
        } catch (Throwable $e) {
            $this->logger->error((string) $e);

            $this->scenarioSchedulerResponse = null;
            $this->responseError = $e;
        }

        return $this->scenarioSchedulerResponse;
    }
}
