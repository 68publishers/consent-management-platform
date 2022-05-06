<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ResetPassword;

use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\Web\Ui\Form\FormFactoryInterface;
use Nepada\FormRenderer\TemplateRenderer;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\FrontModule\Control\ResetPassword\Event\PasswordResetEvent;
use App\Web\FrontModule\Control\ResetPassword\Event\PasswordResetFailedEvent;
use SixtyEightPublishers\ForgotPasswordBundle\Application\Helper\ServerHelpers;
use App\Web\FrontModule\Control\ResetPassword\Event\PasswordRequestExpiredEvent;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\ValueObject\PasswordRequestId;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Command\CompletePasswordRequestCommand;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Exception\EmailAddressNotFoundException;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Exception\PasswordStatusChangeException;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Exception\PasswordRequestExpiredException;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Exception\PasswordRequestNotFoundException;

final class ResetPasswordControl extends Control
{
	use FormFactoryOptionsTrait;

	private PasswordRequestId $passwordRequestId;

	private CommandBusInterface $commandBus;

	private FormFactoryInterface $formFactory;

	/**
	 * @param \SixtyEightPublishers\ForgotPasswordBundle\Domain\ValueObject\PasswordRequestId $passwordRequestId
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface                $commandBus
	 * @param \App\Web\Ui\Form\FormFactoryInterface                                           $formFactory
	 */
	public function __construct(PasswordRequestId $passwordRequestId, CommandBusInterface $commandBus, FormFactoryInterface $formFactory)
	{
		$this->passwordRequestId = $passwordRequestId;
		$this->commandBus = $commandBus;
		$this->formFactory = $formFactory;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$renderer = $form->getRenderer();
		assert($renderer instanceof TemplateRenderer);

		$form->setTranslator($this->getPrefixedTranslator());
		$renderer->importTemplate(__DIR__ . '/templates/form.imports.latte');

		$form->addPassword('password')
			->setRequired('password.required')
			->setHtmlAttribute('placeholder', 'password.field')
			->setHtmlAttribute('autocomplete', 'new-password');

		$form->addSubmit('send', 'send.field');

		$form->onSuccess[] = function (Form $form) {
			$this->resetPassword($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function resetPassword(Form $form): void
	{
		$password = $form->values->password;

		try {
			$this->commandBus->dispatch(CompletePasswordRequestCommand::create($this->passwordRequestId->toString(), $password, ServerHelpers::getIpAddress(), ServerHelpers::getUserAgent()));

			$this->dispatchEvent(new PasswordResetEvent());
		} catch (PasswordRequestExpiredException $e) {
			$this->dispatchEvent(new PasswordRequestExpiredEvent());
		} catch (EmailAddressNotFoundException|PasswordRequestNotFoundException|PasswordStatusChangeException $e) {
			$this->dispatchEvent(new PasswordResetFailedEvent($e));
		}
	}
}
