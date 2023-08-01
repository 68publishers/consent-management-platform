<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectCreatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControl;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControlFactoryInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectResource::class, privilege: ProjectResource::CREATE)]
final class AddProjectPresenter extends AdminPresenter
{
    public function __construct(
        private readonly ProjectFormControlFactoryInterface $projectFormControlFactory,
    ) {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentProjectForm(): ProjectFormControl
    {
        $control = $this->projectFormControlFactory->create();

        $control->addEventListener(ProjectCreatedEvent::class, function (ProjectCreatedEvent $event) {
            $this->subscribeFlashMessage(FlashMessage::success('project_created'));
            $this->redirect('Consents:', ['project' => $event->code()]);
        });

        $control->addEventListener(ProjectFormProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('project_creation_failed'));
        });

        return $control;
    }
}
