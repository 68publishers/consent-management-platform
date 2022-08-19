<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use NasExt\Forms\DependentData;
use App\Application\Localization\Locales;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Application\GlobalSettings\Locale;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use NasExt\Forms\Controls\DependentSelectBox;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Domain\GlobalSettings\Command\PutLocalizationSettingsCommand;
use App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm\Event\LocalizationSettingsUpdatedEvent;
use App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm\Event\LocalizationSettingsUpdateFailedEvent;

final class LocalizationSettingsFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private GlobalSettingsInterface $globalSettings;

	private Locales $locales;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Application\GlobalSettings\GlobalSettingsInterface          $globalSettings
	 * @param \App\Application\Localization\Locales                            $locales
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, GlobalSettingsInterface $globalSettings, Locales $locales)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->globalSettings = $globalSettings;
		$this->locales = $locales;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$localeList = $this->getLocales();

		$form->setTranslator($this->getPrefixedTranslator());

		$form->addMultiSelect('locales', 'locales.field', $localeList)
			->checkDefaultValue(FALSE)
			->setTranslator(NULL)
			->setOption('searchbar', TRUE)
			->setOption('tags', TRUE)
			->setRequired('locales.required');

		$form->addComponent(
			(new DependentSelectBox('default_locale.field', [$form->getComponent('locales')]))
				->setDependentCallback(function ($values) use ($localeList) {
					$locales = $values['locales'];

					if (empty($locales)) {
						return new DependentData([]);
					}

					$defaultValue = $this->globalSettings->defaultLocale()->code();
					$defaultValue = in_array($defaultValue, $locales, TRUE) ? $defaultValue : NULL;

					if (NULL === $defaultValue && 0 < count($locales)) {
						$defaultValue = reset($locales);
					}

					return new DependentData(
						array_filter($localeList, static fn (string $loc): bool => in_array($loc, $locales, TRUE), ARRAY_FILTER_USE_KEY),
						$defaultValue
					);
				})
				->setPrompt('-------')
				->checkDefaultValue(FALSE)
				->setTranslator(NULL)
				->setRequired('default_locale.required'),
			'default_locale'
		);

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'save.field');

		$form->setDefaults([
			'locales' => array_map(static fn (Locale $locale): string => $locale->code(), $this->globalSettings->locales()),
			'default_locale' => $this->globalSettings->defaultLocale()->code(),
		]);

		$form->onSuccess[] = function (Form $form): void {
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
		$command = PutLocalizationSettingsCommand::create($values->locales, $values->default_locale);

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new LocalizationSettingsUpdateFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new LocalizationSettingsUpdatedEvent());
		$this->redrawControl();
	}

	/**
	 * @return array
	 */
	private function getLocales(): array
	{
		$list = $this->locales->get();

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
