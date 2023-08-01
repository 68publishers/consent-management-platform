<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerList;

use App\Application\Acl\CrawlerScenarioSchedulersResource;
use App\Application\Crawler\CrawlerClientProvider;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use App\ReadModel\Project\ProjectSelectOptionView;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\DeleteScenarioSchedulerModalControl;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\DeleteScenarioSchedulerModalControlFactoryInterface;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\Event\FailedToDeleteScenarioSchedulerEvent;
use App\Web\AdminModule\CrawlerModule\Control\DeleteScenarioScheduler\Event\ScenarioSchedulerDeletedEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\FailedToUpdateScenarioSchedulerEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\ScenarioSchedulerUpdatedEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\ScenarioSchedulerFormControl;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\ScenarioSchedulerFormModalControl;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\ScenarioSchedulerFormModalControlFactoryInterface;
use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\DataGrid\DataSource\CrawlerDataSource;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use Nette\Application\UI\Multiplier;
use Nette\InvalidStateException;
use Nette\Localization\Translator;
use Ramsey\Uuid\Uuid;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulersController;
use SixtyEightPublishers\CrawlerClient\Exception\NotFoundException;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use Throwable;
use Ublaboo\DataGrid\Exception\DataGridException;

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
        DeleteScenarioSchedulerModalControlFactoryInterface $deleteScenarioSchedulerModalControlFactory,
    ) {
        $this->dataGridFactory = $dataGridFactory;
        $this->crawlerClientProvider = $crawlerClientProvider;
        $this->queryBus = $queryBus;
        $this->scenarioSchedulerFormModalControlFactory = $scenarioSchedulerFormModalControlFactory;
        $this->deleteScenarioSchedulerModalControlFactory = $deleteScenarioSchedulerModalControlFactory;
    }

    public function handleChangeActiveState(string $scenarioSchedulerId, bool $active): void
    {
        try {
            $controller = $this->crawlerClientProvider->get()->getController(ScenarioSchedulersController::class);

            if ($active) {
                $controller->activateScenarioScheduler($scenarioSchedulerId);
            } else {
                $controller->deactivateScenarioScheduler($scenarioSchedulerId);
            }

            $this->subscribeFlashMessage(FlashMessage::success('scenario_scheduler_' . ($active ? 'activated' : 'deactivated')));
        } catch (NotFoundException $e) {
            $this->subscribeFlashMessage(FlashMessage::error('failed_to_' . ($active ? 'activate' : 'deactivate') . '_scenario_scheduler.not_found'));
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
            $this->subscribeFlashMessage(FlashMessage::error('failed_to_' . ($active ? 'activate' : 'deactivate') . '_scenario_scheduler.generic'));
        }

        $this['grid']->reload();
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

        $translator = $grid->getTranslator();

        $grid->addColumnText('name', 'name', 'name')
            ->setFilterText();

        $grid->addColumnText('project', 'project')
            ->setFilterSelect($this->getProjectOptions($grid->getTranslator()), 'flags->projectId');

        $grid->addColumnText('active', 'active')
            ->setFilterSelect(FilterHelper::all($translator) + [
                1 => $translator->translate('active_state.active'),
                0 => $translator->translate('active_state.inactive'),
            ]);

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

        $grid->addAction('changeActiveState', '')
            ->setTemplate(__DIR__ . '/templates/action.changeActiveState.latte');

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

            $control->setInnerControlCreationCallback(function (ScenarioSchedulerFormControl $innerControl) use ($scenarioSchedulerId): void {
                $innerControl->addEventListener(ScenarioSchedulerUpdatedEvent::class, function () use ($scenarioSchedulerId) {
                    $this->subscribeFlashMessage(FlashMessage::success('scenario_scheduler_updated'));
                    $this['grid']->redrawItem($scenarioSchedulerId);
                    $this->closeModal();
                });

                $innerControl->addEventListener(FailedToUpdateScenarioSchedulerEvent::class, function () {
                    $this->subscribeFlashMessage(FlashMessage::error('failed_to_update_scenario_scheduler'));
                });
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
