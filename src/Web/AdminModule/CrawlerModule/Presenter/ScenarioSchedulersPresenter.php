<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Presenter;

use App\Application\Acl\CrawlerScenarioSchedulersResource;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\FailedToCreateScenarioSchedulerEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\ScenarioSchedulerCreatedEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\ScenarioSchedulerFormControl;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\ScenarioSchedulerFormModalControl;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\ScenarioSchedulerFormModalControlFactoryInterface;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerList\ScenarioSchedulerListControl;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerList\ScenarioSchedulerListControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use Nette\Application\BadRequestException;
use Nette\InvalidStateException;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\UserBundle\Application\Exception\IdentityException;

#[Allowed(resource: CrawlerScenarioSchedulersResource::class, privilege: CrawlerScenarioSchedulersResource::READ)]
final class ScenarioSchedulersPresenter extends AdminPresenter
{
    public function __construct(
        private readonly ScenarioSchedulerListControlFactoryInterface $scenarioSchedulerListControlFactory,
        private readonly ScenarioSchedulerFormModalControlFactoryInterface $scenarioSchedulerFormModalControlFactory,
    ) {
        parent::__construct();
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

    protected function createComponentScenarioSchedulerList(): ScenarioSchedulerListControl
    {
        return $this->scenarioSchedulerListControlFactory->create();
    }

    protected function createComponentCreateScenarioSchedulerModal(): ScenarioSchedulerFormModalControl
    {
        if (!$this->getUser()->isAllowed(CrawlerScenarioSchedulersResource::class, CrawlerScenarioSchedulersResource::CREATE)) {
            throw new InvalidStateException('The user is not allowed to create scenario scheduler.');
        }

        $control = $this->scenarioSchedulerFormModalControlFactory->create();

        $control->setInnerControlCreationCallback(function (ScenarioSchedulerFormControl $innerControl): void {
            $innerControl->addEventListener(ScenarioSchedulerCreatedEvent::class, function () {
                $this->subscribeFlashMessage(FlashMessage::success('scenario_scheduler_created'));
                $this->redrawControl('scenario_scheduler_list');
                $this->closeModal();
            });

            $innerControl->addEventListener(FailedToCreateScenarioSchedulerEvent::class, function () {
                $this->subscribeFlashMessage(FlashMessage::error('failed_to_create_scenario_scheduler'));
            });
        });

        return $control;
    }
}
