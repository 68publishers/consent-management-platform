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

		$enabledField = $form->addCheckbox('enabled', 'enabled.field');

		$enabledField->addCondition($form::EQUAL, TRUE)
			->toggle('#' . $this->getUniqueId() . '-host_url-container')
			->toggle('#' . $this->getUniqueId() . '-username-container')
			->toggle('#' . $this->getUniqueId() . '-password-container')
			->toggle('#' . $this->getUniqueId() . '-callback_uri_token-container');

		$form->addText('host_url', 'host_url.field')
			->setOption('id', $this->getUniqueId() . '-host_url-container')
			->addConditionOn($enabledField, $form::EQUAL, TRUE)
				->setRequired('host_url.required')
				->addRule($form::URL, 'host_url.rule.url');

		$form->addText('username', 'username.field')
			->setOption('id', $this->getUniqueId() . '-username-container')
			->addConditionOn($enabledField, $form::EQUAL, TRUE)
				->setRequired('username.required');

		$form->addText('password', 'password.field')
			->setOption('id', $this->getUniqueId() . '-password-container');

		$form->addText('callback_uri_token', 'callback_uri_token.field')
			->setOption('id', $this->getUniqueId() . '-callback_uri_token-container')
			->addConditionOn($enabledField, $form::EQUAL, TRUE)
				->setRequired('callback_uri_token.required');

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'save.field');

		$defaults = $this->globalSettings->crawlerSettings();

		$form->setDefaults([
			'enabled' => $defaults->enabled(),
			'host_url' => (string) $defaults->hostUrl(),
			'username' => (string) $defaults->username(),
			'password' => (string) $defaults->password(),
			'callback_uri_token' => (string) $defaults->callbackUriToken(),
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
			$values->enabled,
			$values->host_url ?: NULL,
			$values->username ?: NULL,
			$values->password,
			$values->callback_uri_token ?: NULL,
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
