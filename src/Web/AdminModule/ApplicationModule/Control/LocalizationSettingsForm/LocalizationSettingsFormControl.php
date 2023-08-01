<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm;

use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Application\GlobalSettings\Locale;
use App\Application\Localization\Locales;
use App\Domain\GlobalSettings\Command\PutLocalizationSettingsCommand;
use App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm\Event\LocalizationSettingsUpdatedEvent;
use App\Web\AdminModule\ApplicationModule\Control\LocalizationSettingsForm\Event\LocalizationSettingsUpdateFailedEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use NasExt\Forms\Controls\DependentSelectBox;
use NasExt\Forms\DependentData;
use Nette\Application\UI\Form;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use Throwable;

final class LocalizationSettingsFormControl extends Control
{
    use FormFactoryOptionsTrait;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly CommandBusInterface $commandBus,
        private readonly GlobalSettingsInterface $globalSettings,
        private readonly Locales $locales,
    ) {}

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create($this->getFormFactoryOptions());
        $localeList = $this->getLocales();

        $form->setTranslator($this->getPrefixedTranslator());

        $form->addMultiSelect('locales', 'locales.field', $localeList)
            ->checkDefaultValue(false)
            ->setTranslator(null)
            ->setOption('searchbar', true)
            ->setOption('tags', true)
            ->setRequired('locales.required');

        $form->addComponent(
            (new DependentSelectBox('default_locale.field', [$form->getComponent('locales')]))
                ->setDependentCallback(function ($values) use ($localeList) {
                    $locales = $values['locales'];

                    if (empty($locales)) {
                        return new DependentData([]);
                    }

                    $defaultValue = $this->globalSettings->defaultLocale()->code();
                    $defaultValue = in_array($defaultValue, $locales, true) ? $defaultValue : null;

                    if (null === $defaultValue && 0 < count($locales)) {
                        $defaultValue = reset($locales);
                    }

                    return new DependentData(
                        array_filter($localeList, static fn (string $loc): bool => in_array($loc, $locales, true), ARRAY_FILTER_USE_KEY),
                        $defaultValue,
                    );
                })
                ->setPrompt('-------')
                ->checkDefaultValue(false)
                ->setTranslator(null)
                ->setRequired('default_locale.required'),
            'default_locale',
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

    private function getLocales(): array
    {
        $list = $this->locales->get();

        foreach ($list as $locale => $name) {
            $list[$locale] = sprintf(
                '%s - %s',
                $name,
                $locale,
            );
        }

        return $list;
    }
}
