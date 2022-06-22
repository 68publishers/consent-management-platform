<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\PasswordChange;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\ReadModel\User\UserView;
use Nette\Forms\Controls\TextInput;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\Password;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\UpdateUserCommand;
use App\Web\AdminModule\ProfileModule\Control\PasswordChange\Event\PasswordChangedEvent;
use App\Web\AdminModule\ProfileModule\Control\PasswordChange\Event\PasswordChangeFailedEvent;

final class PasswordChangeControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private UserView $userView;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\ReadModel\User\UserView                                     $userView
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, UserView $userView)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->userView = $userView;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());

		$form->setTranslator($this->getPrefixedTranslator());

		$form->addPassword('old_password', 'old_password.field')
			->setRequired('old_password.required');

		$form->addPassword('new_password', 'new_password.field')
			->setRequired('new_password.required');

		$form->addPassword('new_password_verify', 'new_password_verify.field')
			->setRequired('new_password_verify.required')
			->addRule($form::EQUAL, 'new_password_verify.rule_equal', $form->getComponent('new_password'))
			->setOmitted();

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'save.field');

		$form->onSuccess[] = function (Form $form): void {
			$this->changePassword($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function changePassword(Form $form): void
	{
		$values = $form->values;

		if (!$this->userView->password->verify(Password::fromValue($values->old_password))) {
			$oldPasswordField = $form->getComponent('old_password');
			assert($oldPasswordField instanceof TextInput);

			$oldPasswordField->addError('old_password.error.invalid_password');

			return;
		}

		$command = UpdateUserCommand::create($this->userView->id->toString())
			->withPassword($values->new_password);

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new PasswordChangeFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new PasswordChangedEvent($this->userView->id));
		$this->redrawControl();
	}
}
