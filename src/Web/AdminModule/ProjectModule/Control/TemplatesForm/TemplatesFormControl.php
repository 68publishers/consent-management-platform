<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\TemplatesForm;

use App\Api\V1\Controller\CookiesController;
use App\Application\GlobalSettings\GlobalSettingsInterface;
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
use App\Web\Utils\ProjectEnvironmentOptions;
use Nette\Application\UI\Form;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Throwable;

final class TemplatesFormControl extends Control
{
    use FormFactoryOptionsTrait;

    public function __construct(
        private readonly ProjectView $projectView,
        private readonly ValidLocalesProvider $validLocalesProvider,
        private readonly FormFactoryInterface $formFactory,
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly GlobalSettingsInterface $globalSettings,
    ) {}

    protected function createComponentForm(): Form
    {
        $translator = $this->getPrefixedTranslator();
        $locales = [];

        foreach ($this->validLocalesProvider->getValidLocales() as $locale) {
            $locales[$localeCode = $locale->code()] = $translator->translate('locale_tab', ['code' => $localeCode, 'name' => $locale->name()]);
        }

        $form = $this->formFactory->create(array_merge(
            $this->getFormFactoryOptions(),
            [
                FormFactoryInterface::OPTION_IMPORTS => __DIR__ . '/templates/form.imports.latte',
                FormFactoryInterface::OPTION_TEMPLATE_VARIABLES => [
                    'locales' => $locales,
                    'projectCode' => $this->projectView->code->value(),
                    'environments' => ProjectEnvironmentOptions::create(
                        environmentSettings: $this->globalSettings->environmentSettings(),
                        projectEnvironments: $this->projectView->environments,
                        translator: $translator,
                        additionalMapper: function (object $environment): object {
                            $environment->link = CookiesController::getTemplateUrl(
                                projectCode: $this->projectView->code->value(),
                                locale: '__LOCALE__',
                                environment: $environment->code,
                            );

                            return $environment;
                        },
                    ),
                ],
            ],
        ));

        $form->setTranslator($translator);

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
