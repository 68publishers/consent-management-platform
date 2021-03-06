<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\ForgotPassword;

use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\Web\Ui\Form\RecaptchaResolver;
use App\Web\Ui\Form\FormFactoryInterface;
use Nepada\FormRenderer\TemplateRenderer;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\FrontModule\Control\ForgotPassword\Event\EmailAddressNotFoundEvent;
use SixtyEightPublishers\ForgotPasswordBundle\Application\Helper\ServerHelpers;
use App\Web\FrontModule\Control\ForgotPassword\Event\PasswordChangeRequestedEvent;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Command\RequestPasswordChangeCommand;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Exception\EmailAddressNotFoundException;

final class ForgotPasswordControl extends Control
{
	use FormFactoryOptionsTrait;

	private CommandBusInterface $commandBus;

	private FormFactoryInterface $formFactory;

	private RecaptchaResolver $recaptchaResolver;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \App\Web\Ui\Form\RecaptchaResolver                               $recaptchaResolver
	 */
	public function __construct(CommandBusInterface $commandBus, FormFactoryInterface $formFactory, RecaptchaResolver $recaptchaResolver)
	{
		$this->commandBus = $commandBus;
		$this->formFactory = $formFactory;
		$this->recaptchaResolver = $recaptchaResolver;
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

		$form->addText('email_address')
			->setRequired('email_address.required')
			->addRule($form::EMAIL, 'email_address.rule')
			->setHtmlAttribute('placeholder', 'email_address.field')
			->setHtmlAttribute('autocomplete', 'username');

		if ($this->recaptchaResolver->isEnabled()) {
			/** @noinspection PhpUndefinedMethodInspection */
			$form->addInvisibleReCaptcha('recaptcha')
				->setRequired(TRUE)
				->setMessage('recaptcha.required');
		}

		$form->addSubmit('send', 'send.field');

		$form->onSuccess[] = function (Form $form): void {
			$this->createPasswordRequest($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function createPasswordRequest(Form $form): void
	{
		$emailAddress = $form->values->email_address;

		try {
			$this->commandBus->dispatch(RequestPasswordChangeCommand::create($emailAddress, ServerHelpers::getIpAddress(), ServerHelpers::getUserAgent()));

			$this->dispatchEvent(new PasswordChangeRequestedEvent($emailAddress));
		} catch (EmailAddressNotFoundException $e) {
			$this->dispatchEvent(new EmailAddressNotFoundEvent($emailAddress));
		}
	}
}
