<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

use Throwable;
use Nette\Security\User;
use App\Web\Ui\Modal\AbstractModalControl;
use Nette\Application\BadRequestException;
use App\Application\Crawler\CrawlerClientProvider;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenarioResponse;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenariosController;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;

final class ScenarioDetailModalControl extends AbstractModalControl
{
	private string $scenarioId;

	private CrawlerClientProvider $crawlerClientProvider;

	private ScenarioDetailControlFactoryInterface $scenarioDetailControlFactory;

	private User $user;

	private ?ScenarioResponse $scenarioResponse = NULL;

	private ?string $serializedScenarioConfig = NULL;

	private ?Throwable $responseError = NULL;

	public function __construct(
		string $scenarioId,
		CrawlerClientProvider $crawlerClientProvider,
		ScenarioDetailControlFactoryInterface $scenarioDetailControlFactory,
		User $user
	) {
		$this->scenarioId = $scenarioId;
		$this->crawlerClientProvider = $crawlerClientProvider;
		$this->scenarioDetailControlFactory = $scenarioDetailControlFactory;
		$this->user = $user;
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		assert($template instanceof ScenarioDetailModalTemplate);

		$template->scenarioId = $this->scenarioId;
		$template->scenarioResponse = $this->getScenarioResponse();
		$template->responseError = $this->responseError;
		$template->user = $this->user;
	}

	/**
	 * @throws BadRequestException
	 */
	protected function createComponentDetail(): ScenarioDetailControl
	{
		$response = $this->getScenarioResponse();

		if (NULL === $response) {
			$this->error(sprintf(
				'Unable to fetch response for scenario %s. %s',
				$this->scenarioId,
				NULL !== $this->responseError ? (string) $this->responseError : '',
			));
		}

		return $this->scenarioDetailControlFactory->create($response->getBody(), $this->serializedScenarioConfig ?? '{}');
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

			$this->serializedScenarioConfig = $client
				->getSerializer()
				->serialize($this->scenarioResponse->getBody()->config);
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
