<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerList;

use Ramsey\Uuid\Uuid;
use App\Web\Ui\Control;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use Nette\Localization\Translator;
use Nette\Application\UI\Multiplier;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use Ublaboo\DataGrid\Exception\DataGridException;
use App\Application\Crawler\CrawlerClientProvider;
use App\ReadModel\Project\ProjectSelectOptionView;
use App\Web\Ui\DataGrid\DataSource\CrawlerDataSource;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use App\Application\Acl\CrawlerScenarioSchedulersResource;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\ScenarioSchedulerFormModalControl;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\ScenarioSchedulerUpdatedEvent;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\DeleteScenarioSchedulerModalControl;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\Event\ScenarioSchedulerDeletedEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\FailedToUpdateScenarioSchedulerEvent;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\Event\FailedToDeleteScenarioSchedulerEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\ScenarioSchedulerFormModalControlFactoryInterface;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\DeleteScenarioSchedulerModalControlFactoryInterface;

final class ScenarioSchedulerListControl extends Control
{
	private DataGridFactoryInterface $dataGridFactory;

	private CrawlerClientProvider $crawlerClientProvider;

	private QueryBusInterface $queryBus;

	private ScenarioSchedulerFormModalControlFactoryInterface $scenarioSchedulerFormModalControlFactory;

	private DeleteScenarioSchedulerModalControlFactoryInterface $deleteScenarioSchedulerModalControlFactory;

	public function __construct(
		DataGridFactoryInterface $dataGridFactory,
		CrawlerClientProvider $crawlerClientProvider,
		QueryBusInterface $queryBus,
		ScenarioSchedulerFormModalControlFactoryInterface $scenarioSchedulerFormModalControlFactory,
		DeleteScenarioSchedulerModalControlFactoryInterface $deleteScenarioSchedulerModalControlFactory
	) {
		$this->dataGridFactory = $dataGridFactory;
		$this->crawlerClientProvider = $crawlerClientProvider;
		$this->queryBus = $queryBus;
		$this->scenarioSchedulerFormModalControlFactory = $scenarioSchedulerFormModalControlFactory;
		$this->deleteScenarioSchedulerModalControlFactory = $deleteScenarioSchedulerModalControlFactory;
	}

	/**
	 * @throws DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create();
		$dataSource = CrawlerDataSource::scenarioSchedulers($this->crawlerClientProvider);

		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');
		$grid->setDataSource($dataSource);

		$grid->addTemplateVariable('getDataSourceError', function () use ($dataSource) {
			return $dataSource->getError();
		});

		$grid->setTranslator($this->getPrefixedTranslator());

		$grid->addColumnText('name', 'name', 'name')
			->setFilterText();

		$grid->addColumnText('project', 'project')
			->setFilterSelect($this->getProjectOptions($grid->getTranslator()), 'flags->projectId');

		$grid->addColumnText('expression', 'expression', 'expression');

		$grid->addColumnDateTimeTz('createdAt', 'createdAt', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setFilterDate('created');

		$grid->addColumnDateTimeTz('updatedAt', 'updatedAt', 'updatedAt')
			->setFormat('j.n.Y H:i:s')
			->setReplacement([ '' => '-' ])
			->setFilterDate('updated');

		$grid->addAction('edit', '')
			->setTemplate(__DIR__ . '/templates/action.edit.latte');

		$grid->addAction('delete', '')
			->setTemplate(__DIR__ . '/templates/action.delete.latte');

		return $grid;
	}

	protected function createComponentEdit(): Multiplier
	{
		if (!$this->getUser()->isAllowed(CrawlerScenarioSchedulersResource::class, CrawlerScenarioSchedulersResource::UPDATE)) {
			throw new InvalidStateException('The user is not allowed to update scenario scheduler.');
		}

		return new Multiplier(function (string $scenarioSchedulerHexId): ScenarioSchedulerFormModalControl {
			$scenarioSchedulerId = Uuid::fromString($scenarioSchedulerHexId)->toString();
			$control = $this->scenarioSchedulerFormModalControlFactory->create($scenarioSchedulerId);
			$inner = $control->getInnerControl();

			$inner->addEventListener(ScenarioSchedulerUpdatedEvent::class, function () use ($scenarioSchedulerId) {
				$this->subscribeFlashMessage(FlashMessage::success('scenario_scheduler_updated'));
				$this['grid']->redrawItem($scenarioSchedulerId);
				$this->closeModal();
			});

			$inner->addEventListener(FailedToUpdateScenarioSchedulerEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::error('failed_to_update_scenario_scheduler'));
			});

			return $control;
		});
	}

	protected function createComponentDelete(): Multiplier
	{
		if (!$this->getUser()->isAllowed(CrawlerScenarioSchedulersResource::class, CrawlerScenarioSchedulersResource::DELETE)) {
			throw new InvalidStateException('The user is not allowed to delete scenario scheduler.');
		}

		return new Multiplier(function (string $scenarioSchedulerHexId): DeleteScenarioSchedulerModalControl {
			$control = $this->deleteScenarioSchedulerModalControlFactory->create(Uuid::fromString($scenarioSchedulerHexId)->toString());

			$control->addEventListener(ScenarioSchedulerDeletedEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::success('scenario_scheduler_deleted'));
				$this['grid']->reload();
				$this->closeModal();
			});

			$control->addEventListener(FailedToDeleteScenarioSchedulerEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::error('failed_to_delete_scenario_scheduler'));
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
