<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Web\Ui\Form\FormFactoryInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControl;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\DeleteProjectControl;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event\ProjectDeletedEvent;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event\ProjectDeletionFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\DeleteProjectControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectFormProcessingFailedEvent;

final class EditProjectPresenter extends SelectedProjectPresenter
{
	private ProjectFormControlFactoryInterface $projectFormControlFactory;

	private DeleteProjectControlFactoryInterface $deleteProjectControlFactory;

	/**
	 * @param \App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControlFactoryInterface     $projectFormControlFactory
	 * @param \App\Web\AdminModule\ProjectModule\Control\DeleteProject\DeleteProjectControlFactoryInterface $deleteProjectControlFactory
	 */
	public function __construct(ProjectFormControlFactoryInterface $projectFormControlFactory, DeleteProjectControlFactoryInterface $deleteProjectControlFactory)
	{
		parent::__construct();

		$this->projectFormControlFactory = $projectFormControlFactory;
		$this->deleteProjectControlFactory = $deleteProjectControlFactory;
	}

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControl
	 */
	protected function createComponentProjectForm(): ProjectFormControl
	{
		$control = $this->projectFormControlFactory->create($this->projectView);

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
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

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\DeleteProject\DeleteProjectControl
	 */
	protected function createComponentDelete(): DeleteProjectControl
	{
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
