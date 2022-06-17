<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject;

use DomainException;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\ReadModel\Project\ProjectView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Modal\Confirm\ConfirmModalControl;
use App\Domain\Project\Command\DeleteProjectCommand;
use App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event\ProjectDeletedEvent;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event\ProjectDeletionFailedEvent;

final class DeleteProjectControl extends Control
{
	private ProjectView $projectView;

	private CommandBusInterface $commandBus;

	private FormFactoryInterface $formFactory;

	private ConfirmModalControlFactoryInterface $confirmModalControlFactory;

	/**
	 * @param \App\ReadModel\Project\ProjectView                               $projectView
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface    $confirmModalControlFactory
	 */
	public function __construct(ProjectView $projectView, CommandBusInterface $commandBus, FormFactoryInterface $formFactory, ConfirmModalControlFactoryInterface $confirmModalControlFactory)
	{
		$this->projectView = $projectView;
		$this->commandBus = $commandBus;
		$this->formFactory = $formFactory;
		$this->confirmModalControlFactory = $confirmModalControlFactory;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$form->setTranslator($this->getPrefixedTranslator());
		$form->addProtection('//layout.form_protection');
		$form->addSubmit('delete', 'delete.field');

		$form->onSuccess[] = function () {
			$this->handleOpenModal('deleteConfirm');
		};

		return $form;
	}

	/**
	 * @return \App\Web\Ui\Modal\Confirm\ConfirmModalControl
	 */
	protected function createComponentDeleteConfirm(): ConfirmModalControl
	{
		$name = $this->projectView->name->value();

		return $this->confirmModalControlFactory->create(
			'',
			$this->getPrefixedTranslator()->translate('delete_confirm.question', ['name' => $name]),
			function () {
				try {
					$this->commandBus->dispatch(DeleteProjectCommand::create($this->projectView->id->toString()));
					$this->dispatchEvent(new ProjectDeletedEvent($this->projectView->id));
				} catch (DomainException $e) {
					$this->dispatchEvent(new ProjectDeletionFailedEvent($e));
				}

				$this->closeModal();
			}
		);
	}
}
