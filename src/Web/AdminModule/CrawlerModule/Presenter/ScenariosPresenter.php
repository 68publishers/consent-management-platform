<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Presenter;

use Nette\InvalidStateException;
use Nette\Application\BadRequestException;
use App\Application\Acl\CrawlerScenariosResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\UserBundle\Application\Exception\IdentityException;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioList\ScenarioListControl;
use App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm\RunScenarioFormControl;
use App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm\Event\ScenarioRunningEvent;
use App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm\RunScenarioFormModalControl;
use App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm\Event\FailedToRunScenarioEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioList\ScenarioListControlFactoryInterface;
use App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm\RunScenarioFormModalControlFactoryInterface;

#[Allowed(resource: CrawlerScenariosResource::class, privilege: CrawlerScenariosResource::READ)]
final class ScenariosPresenter extends AdminPresenter
{
	private ScenarioListControlFactoryInterface $scenarioListControlFactory;

	private RunScenarioFormModalControlFactoryInterface $runScenarioFormModalControlFactory;

	public function __construct(
		ScenarioListControlFactoryInterface $scenarioListControlFactory,
		RunScenarioFormModalControlFactoryInterface $runScenarioFormModalControlFactory
	) {
		parent::__construct();

		$this->scenarioListControlFactory = $scenarioListControlFactory;
		$this->runScenarioFormModalControlFactory = $runScenarioFormModalControlFactory;
	}

	/**
	 * @throws IdentityException
	 * @throws BadRequestException
	 */
	protected function startup(): void
	{
		parent::startup();

		if (!$this->globalSettings->crawlerSettings()->enabled()) {
			$this->error('Crawler is disabled.');
		}
	}

	protected function createComponentScenarioList(): ScenarioListControl
	{
		return $this->scenarioListControlFactory->create();
	}

	protected function createComponentRunScenarioModal(): RunScenarioFormModalControl
	{
		if (!$this->getUser()->isAllowed(CrawlerScenariosResource::class, CrawlerScenariosResource::RUN)) {
			throw new InvalidStateException('The user is not allowed to run scenario.');
		}

		$control = $this->runScenarioFormModalControlFactory->create();

		$control->setInnerControlCreationCallback(function (RunScenarioFormControl $innerControl): void {
			$innerControl->addEventListener(ScenarioRunningEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::success('scenario_running'));
				$this->redrawControl('scenario_list');
				$this->closeModal();
			});

			$innerControl->addEventListener(FailedToRunScenarioEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::error('failed_to_run_scenario'));
			});
		});

		return $control;
	}
}
