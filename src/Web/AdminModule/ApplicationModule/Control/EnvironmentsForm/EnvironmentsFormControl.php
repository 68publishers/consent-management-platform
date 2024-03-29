<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\EnvironmentsForm;

use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\GlobalSettings\Command\Environment as CommandEnvironment;
use App\Domain\GlobalSettings\Command\PutEnvironmentSettingsCommand;
use App\Web\AdminModule\ApplicationModule\Control\EnvironmentsForm\Event\EnvironmentsUpdatedEvent;
use App\Web\AdminModule\ApplicationModule\Control\EnvironmentsForm\Event\EnvironmentsUpdateFailedEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Web\Ui\Form\Validator\UniqueMultiplierValuesValidator;
use Contributte\FormMultiplier\Multiplier;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use Throwable;

final class EnvironmentsFormControl extends Control
{
    use FormFactoryOptionsTrait;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly CommandBusInterface $commandBus,
        private readonly GlobalSettingsInterface $globalSettings,
    ) {}

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create(array_merge(
            $this->getFormFactoryOptions(),
            [
                FormFactoryInterface::OPTION_IMPORTS => __DIR__ . '/templates/form.imports.latte',
            ],
        ));
        $translator = $this->getPrefixedTranslator();

        $form->setTranslator($translator);

        $defaultEnvironmentContainer = $form->addContainer('defaultEnvironment');

        $defaultEnvironmentContainer->addText('defaultName')
            ->setHtmlAttribute('placeholder', 'defaultName.placeholder')
            ->setRequired('defaultName.required');

        $defaultEnvironmentContainer->addText('defaultColor')
            ->setDefaultValue('#ffffff')
            ->setHtmlAttribute('placeholder', 'defaultColor.placeholder')
            ->setOption('type', 'color-picker')
            ->setOption('placement', 'bottom-left')
            ->setRequired('defaultColor.required')
            ->addRule(Form::Pattern, 'defaultColor.rule.pattern', '#([a-fA-F0-9]{3}){1,2}\b');

        assert(is_callable([$form, 'addMultiplier']));

        $multiplier = $form->addMultiplier('environments', function (Container $container) {
            $container->setMappedType(CommandEnvironment::class);

            $container->addText('code')
                ->setHtmlAttribute('placeholder', 'code.placeholder')
                ->setRequired('code.required')
                ->addRule(Form::Pattern, 'code.rule.pattern', '[a-z0-9_\-\.]+')
                ->addRule(Form::NotEqual, 'code.rule.notEqualDefault', 'default')
                ->addRule(UniqueMultiplierValuesValidator::Validator, 'code.rule.valuesAreNotUnique');

            $container->addText('name')
                ->setHtmlAttribute('placeholder', 'name.placeholder')
                ->setRequired('name.required');

            $container->addText('color')
                ->setDefaultValue('#ffffff')
                ->setHtmlAttribute('placeholder', 'color.placeholder')
                ->setOption('type', 'color-picker')
                ->setOption('placement', 'bottom-left')
                ->setRequired('color.required')
                ->addRule(Form::Pattern, 'color.rule.pattern', '#([a-fA-F0-9]{3}){1,2}\b');
        }, 0);

        assert($multiplier instanceof Multiplier);

        $multiplier->setMappedType('array');

        $multiplier->addCreateButton('addEnvironment')
            ->addOnCreateCallback(function (SubmitButton $button): void {
                $button->renderAsButton();

                $button->onClick[] = function () {
                    $this->redrawControl();
                };
            });

        $multiplier->addRemoveButton('removeEnvironment')
            ->addOnCreateCallback(function (SubmitButton $button): void {
                $button->renderAsButton();

                $button->onClick[] = function () {
                    $this->redrawControl();
                };
            });

        $form->onSuccess[] = function (Form $form): void {
            $this->saveGlobalSettings($form);
        };

        $environmentSettings = $this->globalSettings->environmentSettings();

        $form->setDefaults([
            'defaultEnvironment' => [
                'defaultName' => $environmentSettings->defaultEnvironment->name->value(),
                'defaultColor' => $environmentSettings->defaultEnvironment->color->value(),
            ],
            'environments' => $environmentSettings->environments->toArray(),
        ]);

        $form->addProtection('//layout.form_protection');

        $form->addSubmit('save', 'save.field');

        return $form;
    }

    private function saveGlobalSettings(Form $form): void
    {
        $values = $form->getValues();

        $command = PutEnvironmentSettingsCommand::create(
            defaultEnvironmentName: $values->defaultEnvironment->defaultName,
            defaultEnvironmentColor: $values->defaultEnvironment->defaultColor,
            environments: $values->environments,
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
            $this->dispatchEvent(new EnvironmentsUpdateFailedEvent($e));

            return;
        }

        $this->dispatchEvent(new EnvironmentsUpdatedEvent());
        $this->redrawControl();
    }
}
