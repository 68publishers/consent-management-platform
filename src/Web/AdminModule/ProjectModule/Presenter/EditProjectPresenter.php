<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Web\Ui\Form\FormFactoryInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControl;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectFormProcessingFailedEvent;

final class EditProjectPresenter extends SelectedProjectPresenter
{
	private ProjectFormControlFactoryInterface $projectFormControlFactory;

	/**
	 * @param \App\Web\AdminModule\ProjectModule\Control\ProjectForm\ProjectFormControlFactoryInterface $projectFormControlFactory
	 */
	public function __construct(ProjectFormControlFactoryInterface $projectFormControlFactory)
	{
		parent::__construct();

		$this->projectFormControlFactory = $projectFormControlFactory;
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
		});

		$control->addEventListener(ProjectFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('project_edit_failed'));
		});

		return $control;
	}
}
