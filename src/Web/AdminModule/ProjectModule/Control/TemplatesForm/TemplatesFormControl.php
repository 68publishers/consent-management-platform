<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\TemplatesForm;

use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\Project\Command\UpdateProjectTemplatesCommand;
use App\Domain\Project\Exception\InvalidTemplateException;
use App\ReadModel\Project\FindProjectTemplatesQuery;
use App\ReadModel\Project\ProjectTemplateView;
use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event\TemplatesFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event\TemplatesUpdatedEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use Nepada\FormRenderer\TemplateRenderer;
use Nette\Application\UI\Form;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Throwable;

final class TemplatesFormControl extends Control
{
    use FormFactoryOptionsTrait;

    private ProjectView $projectView;

    private ValidLocalesProvider $validLocalesProvider;

    private FormFactoryInterface $formFactory;

    private CommandBusInterface $commandBus;

    private QueryBusInterface $queryBus;

    public function __construct(ProjectView $projectView, ValidLocalesProvider $validLocalesProvider, FormFactoryInterface $formFactory, CommandBusInterface $commandBus, QueryBusInterface $queryBus)
    {
        $this->projectView = $projectView;
        $this->validLocalesProvider = $validLocalesProvider;
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create($this->getFormFactoryOptions());
        $translator = $this->getPrefixedTranslator();
        $renderer = $form->getRenderer();
        $locales = [];

        assert($renderer instanceof TemplateRenderer);

        foreach ($this->validLocalesProvider->getValidLocales() as $locale) {
            $locales[$localeCode = $locale->code()] = $translator->translate('locale_tab', ['code' => $localeCode, 'name' => $locale->name()]);
        }

        $form->setTranslator($translator);
        $renderer->importTemplate(__DIR__ . '/templates/form.imports.latte');
        $renderer->getTemplate()->locales = $locales;
        $renderer->getTemplate()->projectCode = $this->projectView->code->value();

        $templatesContainer = $form->addContainer('templates');

        foreach (array_keys($locales) as $locale) {
            $templatesContainer->addTextArea($locale, null, null, 4)
                ->setOption('codemirror', 'htmlmixed');
        }

        $form->addProtection('//layout.form_protection');

        $form->addSubmit('save', 'update.field');

        $form->setDefaults([
            'templates' => $this->getDefaultTemplates(),
        ]);

        $form->onSuccess[] = function (Form $form): void {
            $this->saveTemplates($form);
        };

        return $form;
    }

    private function saveTemplates(Form $form): void
    {
        $values = $form->values;
        $command = UpdateProjectTemplatesCommand::create($this->projectView->id->toString());

        foreach ($values->templates as $locale => $template) {
            $command = $command->withTemplate($locale, $template);
        }

        try {
            $this->commandBus->dispatch($command);
        } catch (InvalidTemplateException $e) {
            $form->addError($e->getMessage(), false);
            $this->redrawControl();

            return;
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
            $this->dispatchEvent(new TemplatesFormProcessingFailedEvent($e));

            return;
        }

        $this->dispatchEvent(new TemplatesUpdatedEvent());
        $this->redrawControl();
    }

    private function getDefaultTemplates(): array
    {
        $templates = [];

        foreach ($this->queryBus->dispatch(FindProjectTemplatesQuery::create($this->projectView->id->toString())) as $projectTemplateView) {
            assert($projectTemplateView instanceof ProjectTemplateView);

            $templates[$projectTemplateView->templateLocale->value()] = $projectTemplateView->template->value();
        }

        return $templates;
    }
}
