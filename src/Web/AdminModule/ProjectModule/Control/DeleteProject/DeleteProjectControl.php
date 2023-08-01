<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject;

use App\Domain\Project\Command\DeleteProjectCommand;
use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event\ProjectDeletedEvent;
use App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event\ProjectDeletionFailedEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Modal\Confirm\ConfirmModalControl;
use App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface;
use DomainException;
use Nette\Application\UI\Form;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class DeleteProjectControl extends Control
{
    private ProjectView $projectView;

    private CommandBusInterface $commandBus;

    private FormFactoryInterface $formFactory;

    private ConfirmModalControlFactoryInterface $confirmModalControlFactory;

    public function __construct(ProjectView $projectView, CommandBusInterface $commandBus, FormFactoryInterface $formFactory, ConfirmModalControlFactoryInterface $confirmModalControlFactory)
    {
        $this->projectView = $projectView;
        $this->commandBus = $commandBus;
        $this->formFactory = $formFactory;
        $this->confirmModalControlFactory = $confirmModalControlFactory;
    }

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create([
            FormFactoryInterface::OPTION_AJAX => true,
        ]);

        $form->setTranslator($this->getPrefixedTranslator());
        $form->addProtection('//layout.form_protection');
        $form->addSubmit('delete', 'delete.field');

        $form->onSuccess[] = function (): void {
            $this->handleOpenModal('deleteConfirm');
        };

        return $form;
    }

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
            },
        );
    }
}
