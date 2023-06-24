<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\CrawlerSettingsForm;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\GlobalSettings\Command\PutCrawlerSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\ApplicationModule\Control\CrawlerSettingsForm\Event\CrawlerSettingsUpdatedEvent;
use App\Web\AdminModule\ApplicationModule\Control\CrawlerSettingsForm\Event\CrawlerSettingsUpdateFailedEvent;

final class CrawlerSettingsFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private GlobalSettingsInterface $globalSettings;

	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, GlobalSettingsInterface $globalSettings)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->globalSettings = $globalSettings;
	}

	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($translator);

		$form->addText('host_url', 'host_url.field')
			->setRequired('host_url.required')
			->addRule($form::URL, 'host_url.rule.url');

		$form->addText('username', 'username.field')
			->setRequired('username.required');

		$form->addText('password', 'password.field');

		$form->addText('callback_uri_token', 'callback_uri_token.field')
			->setRequired('callback_uri_token.required');

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'save.field');

		$defaults = $this->globalSettings->crawlerSettings();

		$form->setDefaults([
			'host_url' => $defaults->hostUrl(),
			'username' => $defaults->username(),
			'password' => $defaults->password(),
			'callback_uri_token' => $defaults->callbackUriToken(),
		]);

		$form->onSuccess[] = function (Form $form): void {
			$this->saveGlobalSettings($form);
		};

		return $form;
	}

	private function saveGlobalSettings(Form $form): void
	{
		$values = $form->getValues();
		$command = PutCrawlerSettingsCommand::create(
			$values->host_url,
			$values->username,
			$values->password,
			$values->callback_uri_token,
		);

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new CrawlerSettingsUpdateFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new CrawlerSettingsUpdatedEvent());
		$this->redrawControl();
	}
}
