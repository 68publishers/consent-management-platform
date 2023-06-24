<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\AbortScenario;

use Throwable;
use App\Web\Ui\Modal\AbstractModalControl;
use App\Application\Crawler\CrawlerClientProvider;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenarioResponse;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenariosController;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;
use App\Web\AdminModule\CrawlerModule\Control\AbortScenario\Event\ScenarioAbortedEvent;
use App\Web\AdminModule\CrawlerModule\Control\AbortScenario\Event\FailedToAbortScenarioEvent;

final class AbortScenarioModalControl extends AbstractModalControl
{
	private string $scenarioId;

	private CrawlerClientProvider $crawlerClientProvider;

	private ?ScenarioResponse $scenarioResponse = NULL;

	private ?Throwable $responseError = NULL;

	public function __construct(
		string $scenarioId,
		CrawlerClientProvider $crawlerClientProvider
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
		$template->scenarioResponse = NULL === $this->responseError ? $this->getScenarioResponse() : NULL;
		$template->responseError = $this->responseError;
	}

	private function getScenarioResponse(): ?ScenarioResponse
	{
		if (NULL !== $this->scenarioResponse || NULL !== $this->responseError) {
			return $this->scenarioResponse;
		}

		try {
			$client = $this->crawlerClientProvider->get();
			$this->scenarioResponse = $client
				->getController(ScenariosController::class)
				->getScenario($this->scenarioId);
		} catch (ControllerResponseExceptionInterface $e) {
			$this->scenarioResponse = NULL;
			$this->responseError = $e;
		} catch (Throwable $e) {
			$this->logger->error((string) $e);

			$this->scenarioResponse = NULL;
			$this->responseError = $e;
		}

		return $this->scenarioResponse;
	}
}
