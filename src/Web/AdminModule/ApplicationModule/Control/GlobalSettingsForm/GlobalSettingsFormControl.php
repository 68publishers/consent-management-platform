<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use SixtyEightPublishers\i18n\Lists\LanguageList;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\GlobalSettings\Command\StoreGlobalSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\Event\GlobalSettingsUpdatedEvent;
use App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\Event\GlobalSettingsUpdateFailedEvent;

final class GlobalSettingsFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private GlobalSettingsInterface $globalSettings;

	private LanguageList $languageList;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Application\GlobalSettings\GlobalSettingsInterface          $globalSettings
	 * @param \SixtyEightPublishers\i18n\Lists\LanguageList                    $languageList
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, GlobalSettingsInterface $globalSettings, LanguageList $languageList)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->globalSettings = $globalSettings;
		$this->languageList = $languageList;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());

		$form->setTranslator($this->getPrefixedTranslator());

		$form->addMultiSelect('locales', 'locales.field', $this->getLocales())
			->checkDefaultValue(FALSE)
			->setTranslator(NULL)
			->setOption('searchbar', TRUE)
			->setOption('tags', TRUE);

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'save.field');

		$form->setDefaults([
			'locales' => array_keys($this->globalSettings->getNamedLocales()),
		]);

		$form->onSuccess[] = function (Form $form) {
			$this->saveGlobalSettings($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveGlobalSettings(Form $form): void
	{
		$values = $form->values;
		$command = StoreGlobalSettingsCommand::create($values->locales);

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new GlobalSettingsUpdateFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new GlobalSettingsUpdatedEvent());
		$this->redrawControl();
	}

	/**
	 * @return array
	 */
	private function getLocales(): array
	{
		$list = $this->languageList->getList();

		foreach ($list as $locale => $name) {
			$list[$locale] = sprintf(
				'%s - %s',
				$name,
				$locale
			);
		}

		return $list;
	}
}
