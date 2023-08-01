<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler;

use Throwable;
use App\Web\Ui\Modal\AbstractModalControl;
use App\Application\Crawler\CrawlerClientProvider;
use App\Application\Crawler\CrawlerNotConfiguredException;
use App\Web\AdminModule\CrawlerModule\Control\GetScenarioSchedulerResponseTrait;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulersController;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\Event\ScenarioSchedulerDeletedEvent;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\Event\FailedToDeleteScenarioSchedulerEvent;

final class DeleteScenarioSchedulerModalControl extends AbstractModalControl
{
	use GetScenarioSchedulerResponseTrait;

	private string $scenarioSchedulerId;

	private CrawlerClientProvider $crawlerClientProvider;

	public function __construct(
		string $scenarioSchedulerId,
		CrawlerClientProvider $crawlerClientProvider
	) {
		$this->scenarioSchedulerId = $scenarioSchedulerId;
		$this->crawlerClientProvider = $crawlerClientProvider;
	}

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
		$template->scenarioSchedulerResponse = NULL === $this->responseError ? $this->getScenarioSchedulerResponse($this->crawlerClientProvider->get(), $this->scenarioSchedulerId) : NULL;
		$template->responseError = $this->responseError;
	}
}