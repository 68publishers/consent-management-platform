<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectResource;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\DeleteProjectControl;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\DeleteProjectControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event\ProjectDeletedEvent;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event\ProjectDeletionFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControl;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControlFactoryInterface;
use App\Web\Ui\Form\FormFactoryInterface;
use Nette\InvalidStateException;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectResource::class, privilege: ProjectResource::UPDATE)]
final class EditProjectPresenter extends SelectedProjectPresenter
{
    public function __construct(
        private readonly ProjectFormControlFactoryInterface $projectFormControlFactory,
        private readonly DeleteProjectControlFactoryInterface $deleteProjectControlFactory,
    ) {
        parent::__construct();
    }

    protected function createComponentProjectForm(): ProjectFormControl
    {
        $control = $this->projectFormControlFactory->create($this->projectView);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
        ]);

        $control->addEventListener(ProjectUpdatedEvent::class, function (ProjectUpdatedEvent $event) {
            $this->subscribeFlashMessage(FlashMessage::success('project_edited'));

            if ($event->oldCode() !== $event->newCode()) {
                $this->redirect('this', ['project' => $event->newCode()]);
            }

            $this->refreshProjectView($event->newCode());
            $this->redrawControl('heading');
            $this->redrawControl('before_content');
        });

        $control->addEventListener(ProjectFormProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('project_edit_failed'));
        });

        return $control;
    }

    protected function createComponentDelete(): DeleteProjectControl
    {
        if (!$this->getUser()->isAllowed(ProjectResource::class, ProjectResource::DELETE)) {
            throw new InvalidStateException('The user is not allowed to delete projects.');
        }

        $control = $this->deleteProjectControlFactory->create($this->projectView);

        $control->addEventListener(ProjectDeletedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::success('project_deleted'));
            $this->redirect('Projects:');
        });

        $control->addEventListener(ProjectDeletionFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('project_deletion_failed'));
        });

        return $control;
    }
}
