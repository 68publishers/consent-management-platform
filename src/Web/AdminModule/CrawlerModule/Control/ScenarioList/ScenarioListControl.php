<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioList;

use Ramsey\Uuid\Uuid;
use App\Web\Ui\Control;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use Nette\Localization\Translator;
use Nette\Application\UI\Multiplier;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\Application\Acl\CrawlerScenariosResource;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use Ublaboo\DataGrid\Exception\DataGridException;
use App\Application\Crawler\CrawlerClientProvider;
use App\ReadModel\Project\ProjectSelectOptionView;
use App\Web\Ui\DataGrid\DataSource\CrawlerDataSource;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Web\AdminModule\CrawlerModule\Control\AbortScenario\AbortScenarioModalControl;
use App\Web\AdminModule\CrawlerModule\Control\AbortScenario\Event\ScenarioAbortedEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail\ScenarioDetailModalControl;
use App\Web\AdminModule\CrawlerModule\Control\AbortScenario\Event\FailedToAbortScenarioEvent;
use App\Web\AdminModule\CrawlerModule\Control\AbortScenario\AbortScenarioModalControlFactoryInterface;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail\ScenarioDetailModalControlFactoryInterface;

final class ScenarioListControl extends Control
{
	private DataGridFactoryInterface $dataGridFactory;

	private CrawlerClientProvider $crawlerClientProvider;

	private QueryBusInterface $queryBus;

	private ScenarioDetailModalControlFactoryInterface $scenarioDetailModalControlFactory;

	private AbortScenarioModalControlFactoryInterface $abortScenarioModalControlFactory;

	public function __construct(
		DataGridFactoryInterface $dataGridFactory,
		CrawlerClientProvider $crawlerClientProvider,
		QueryBusInterface $queryBus,
		ScenarioDetailModalControlFactoryInterface $scenarioDetailModalControlFactory,
		AbortScenarioModalControlFactoryInterface $abortScenarioModalControlFactory
	) {
		$this->dataGridFactory = $dataGridFactory;
		$this->crawlerClientProvider = $crawlerClientProvider;
		$this->queryBus = $queryBus;
		$this->scenarioDetailModalControlFactory = $scenarioDetailModalControlFactory;
		$this->abortScenarioModalControlFactory = $abortScenarioModalControlFactory;
	}

	/**
	 * @throws DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create();
		$dataSource = CrawlerDataSource::scenarios($this->crawlerClientProvider);

		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');
		$grid->setDataSource($dataSource);

		$grid->addTemplateVariable('getDataSourceError', function () use ($dataSource) {
			return $dataSource->getError();
		});

		$grid->setTranslator($this->getPrefixedTranslator());

		$grid->addColumnText('name', 'name', 'name')
			->setFilterText();

		$grid->addColumnText('status', 'status', 'status')
			->setFilterSelect(FilterHelper::all($grid->getTranslator()) + [
				'waiting' => 'waiting',
				'running' => 'running',
				'completed' => 'completed',
				'failed' => 'failed',
				'aborted' => 'aborted',
			]);

		$grid->addColumnText('project', 'project')
			->setFilterSelect($this->getProjectOptions($grid->getTranslator()), 'flags->projectId');

		$grid->addColumnDateTimeTz('createdAt', 'createdAt', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setFilterDate('created');

		$grid->addColumnDateTimeTz('finishedAt', 'finishedAt', 'finishedAt')
			->setFormat('j.n.Y H:i:s')
			->setReplacement([ '' => '-' ])
			->setFilterDate('finished');

		$grid->addAction('abort', '')
			->setTemplate(__DIR__ . '/templates/action.abort.latte');

		$grid->addAction('detail', '')
			->setTemplate(__DIR__ . '/templates/action.detail.latte');

		return $grid;
	}

	protected function createComponentDetail(): Multiplier
	{
		return new Multiplier(function (string $scenarioHexId): ScenarioDetailModalControl {
			return $this->scenarioDetailModalControlFactory->create(Uuid::fromString($scenarioHexId)->toString());
		});
	}

	protected function createComponentAbort(): Multiplier
	{
		if (!$this->getUser()->isAllowed(CrawlerScenariosResource::class, CrawlerScenariosResource::ABORT)) {
			throw new InvalidStateException('The user is not allowed to abort scenario.');
		}

		return new Multiplier(function (string $scenarioHexId): AbortScenarioModalControl {
			$control = $this->abortScenarioModalControlFactory->create(Uuid::fromString($scenarioHexId)->toString());

			$control->addEventListener(ScenarioAbortedEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::success('scenario_aborted'));
				$this['grid']->reload();
				$this->closeModal();
			});

			$control->addEventListener(FailedToAbortScenarioEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::error('failed_to_abort_scenario'));
				$this['grid']->reload();
			});

			return $control;
		});
	}

	/**
	 * @return array<string, string>
	 */
	private function getProjectOptions(Translator $translator): array
	{
		$options = [
			FilterHelper::all($translator),
		];

		foreach ($this->queryBus->dispatch(FindProjectSelectOptionsQuery::all()) as $projectSelectOptionView) {
			assert($projectSelectOptionView instanceof ProjectSelectOptionView);
			$options += $projectSelectOptionView->toOption();
		}

		return $options;
	}
}
