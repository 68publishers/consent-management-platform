<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm;

use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\Project\Command\CreateProjectCommand;
use App\Domain\Project\Command\UpdateProjectCommand;
use App\Domain\Project\Exception\CodeUniquenessException;
use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectCreatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectUpdatedEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use NasExt\Forms\Controls\DependentSelectBox;
use NasExt\Forms\DependentData;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use Throwable;

final class ProjectFormControl extends Control
{
    use FormFactoryOptionsTrait;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly CommandBusInterface $commandBus,
        private readonly GlobalSettingsInterface $globalSettings,
        private readonly ?ProjectView $default = null,
    ) {}

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create($this->getFormFactoryOptions());
        $globalLocales = [];

        foreach ($this->globalSettings->locales() as $locale) {
            $globalLocales[$locale->code()] = sprintf('%s - %s', $locale->name(), $locale->code());
        }

        $translator = $this->getPrefixedTranslator();

        $form->setTranslator($translator);

        $form->addText('name', 'name.field')
            ->setRequired('name.required');

        $form->addText('code', 'code.field')
            ->setRequired('code.required')
            ->addRule($form::PATTERN, 'code.rule_pattern', '[a-z0-9_\-\.]+')
            ->addRule($form::MAX_LENGTH, 'code.rule_max_length', Code::MAX_LENGTH)
            ->setOption('description', 'code.description');

        $form->addText('domain', 'domain.field')
            ->setRequired('domain.field')
            ->setOption('description', 'domain.description');

        $form->addText('color', 'color.field')
            ->setRequired('color.required')
            ->addRule($form::PATTERN, 'color.rule_pattern', '#([a-fA-F0-9]{3}){1,2}\b')
            ->setOption('description', 'color.description');

        $form->addCheckbox('active', 'active.field')
            ->setDefaultValue(true);

        $form->addMultiSelect('locales', 'locales.field', $globalLocales)
            ->checkDefaultValue(false)
            ->setTranslator(null)
            ->setOption('tags', true)
            ->setRequired('locales.required');

        $form->addComponent(
            (new DependentSelectBox('default_locale.field', [$form->getComponent('locales')]))
                ->setDependentCallback(function ($values) use ($globalLocales) {
                    $locales = $values['locales'];

                    if (empty($locales)) {
                        return new DependentData([]);
                    }

                    $defaultValue = $this->default?->locales->defaultLocale()->value();
                    $defaultValue = in_array($defaultValue, $locales, true) ? $defaultValue : null;

                    if (null === $defaultValue && 0 < count($locales)) {
                        $defaultValue = reset($locales);
                    }

                    return new DependentData(
                        array_filter($globalLocales, static fn (string $loc): bool => in_array($loc, $locales, true), ARRAY_FILTER_USE_KEY),
                        $defaultValue,
                    );
                })
                ->setPrompt('-------')
                ->checkDefaultValue(false)
                ->setTranslator(null)
                ->setRequired('default_locale.required'),
            'default_locale',
        );

        $form->addTextArea('description', 'description.field', null, 4);

        $form->addProtection('//layout.form_protection');

        $form->addSubmit('save', null === $this->default ? 'save.field' : 'update.field');

        if (null !== $this->default) {
            $form->setDefaults([
                'name' => $this->default->name->value(),
                'code' => $this->default->code->value(),
                'domain' => $this->default->domain->value(),
                'color' => $this->default->color->value(),
                'active' => $this->default->active,
                'locales' => $this->default->locales->locales()->toArray(),
                'default_locale' => $this->default->locales->defaultLocale()->value(),
                'description' => $this->default->description->value(),
            ]);
        }

        $form->onSuccess[] = function (Form $form): void {
            $this->saveProject($form);
        };

        return $form;
    }

    private function saveProject(Form $form): void
    {
        $values = $form->values;

        if (null === $this->default) {
            $projectId = ProjectId::new();
            $command = CreateProjectCommand::create(
                $values->name,
                $values->code,
                $values->domain,
                $values->description,
                $values->color,
                $values->active,
                $values->locales,
                $values->default_locale,
                $projectId->toString(),
            );
        } else {
            $projectId = $this->default->id;
            $command = UpdateProjectCommand::create($projectId->toString())
                ->withName($values->name)
                ->withCode($values->code)
                ->withDomain($values->domain)
                ->withDescription($values->description)
                ->withColor($values->color)
                ->withActive($values->active)
                ->withLocales($values->locales, $values->default_locale);
        }

        try {
            $this->commandBus->dispatch($command);
        } catch (CodeUniquenessException $e) {
            $codeField = $form->getComponent('code');
            assert($codeField instanceof TextInput);

            $codeField->addError('code.error.duplicated_value');

            return;
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
            $this->dispatchEvent(new ProjectFormProcessingFailedEvent($e));

            return;
        }

        $this->dispatchEvent(null === $this->default ? new ProjectCreatedEvent($projectId, $values->code) : new ProjectUpdatedEvent($projectId, $this->default->code->value(), $values->code));
        $this->redrawControl();
    }
}
