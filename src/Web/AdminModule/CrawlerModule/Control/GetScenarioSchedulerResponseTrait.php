<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control;

use Throwable;
use SixtyEightPublishers\CrawlerClient\CrawlerClientInterface;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulerResponse;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulersController;

trait GetScenarioSchedulerResponseTrait
{
	protected ?ScenarioSchedulerResponse $scenarioSchedulerResponse = NULL;

	protected ?Throwable $responseError = NULL;

	protected function getScenarioSchedulerResponse(CrawlerClientInterface $client, ?string $scenarioSchedulerId): ?ScenarioSchedulerResponse
	{
		if (NULL === $scenarioSchedulerId || NULL !== $this->scenarioSchedulerResponse || NULL !== $this->responseError) {
			return $this->scenarioSchedulerResponse;
		}

		try {
			$this->scenarioSchedulerResponse = $client
				->getController(ScenarioSchedulersController::class)
				->getScenarioScheduler($scenarioSchedulerId);
		} catch (ControllerResponseExceptionInterface $e) {
			$this->scenarioSchedulerResponse = NULL;
			$this->responseError = $e;
		} catch (Throwable $e) {
			$this->logger->error((string) $e);

			$this->scenarioSchedulerResponse = NULL;
			$this->responseError = $e;
		}

		return $this->scenarioSchedulerResponse;
	}
}
