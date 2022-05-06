<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use SixtyEightPublishers\UserBundle\ReadModel\View\UserView;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\UpdateUserCommand;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event\BasicInformationUpdatedEvent;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event\BasicInformationUpdateFailedEvent;

final class BasicInformationControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private UserView $userView;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\UserBundle\ReadModel\View\UserView         $userView
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, UserView $userView)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->userView = $userView;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->getTemplate()->userView = $this->userView;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());

		$form->setTranslator($this->getPrefixedTranslator());

		$form->addText('firstname', 'firstname.field')
			->setRequired('firstname.required');

		$form->addText('surname', 'surname.field')
			->setRequired('surname.required');

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'save.field');

		$form->setDefaults([
			'firstname' => $this->userView->name->firstname(),
			'surname' => $this->userView->name->surname(),
		]);

		$form->onSuccess[] = function (Form $form) {
			$this->saveBasicInformation($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveBasicInformation(Form $form): void
	{
		$values = $form->values;
		$command = UpdateUserCommand::create($this->userView->id->toString())
			->withFirstname($values->firstname)
			->withSurname($values->surname);

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new BasicInformationUpdateFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new BasicInformationUpdatedEvent($this->userView->id));
		$this->redrawControl();
	}
}
