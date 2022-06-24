<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\TemplatesForm;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\Web\Ui\Form\FormFactoryInterface;
use Nepada\FormRenderer\TemplateRenderer;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Project\ProjectTemplateView;
use App\ReadModel\Project\FindProjectTemplatesQuery;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\Project\Exception\InvalidTemplateException;
use App\Domain\Project\Command\UpdateProjectTemplatesCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event\TemplatesUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event\TemplatesFormProcessingFailedEvent;

final class TemplatesFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private ProjectId $projectId;

	private ValidLocalesProvider $validLocalesProvider;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId                        $projectId
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider             $validLocalesProvider
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 */
	public function __construct(ProjectId $projectId, ValidLocalesProvider $validLocalesProvider, FormFactoryInterface $formFactory, CommandBusInterface $commandBus, QueryBusInterface $queryBus)
	{
		$this->projectId = $projectId;
		$this->validLocalesProvider = $validLocalesProvider;
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
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

		$templatesContainer = $form->addContainer('templates');

		foreach (array_keys($locales) as $locale) {
			$templatesContainer->addTextArea($locale, NULL, NULL, 4)
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

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveTemplates(Form $form): void
	{
		$values = $form->values;
		$command = UpdateProjectTemplatesCommand::create($this->projectId->toString());

		foreach ($values->templates as $locale => $template) {
			$command = $command->withTemplate($locale, $template);
		}

		try {
			$this->commandBus->dispatch($command);
		} catch (InvalidTemplateException $e) {
			$form->addError($e->getMessage(), FALSE);
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

	/**
	 * @return array
	 */
	private function getDefaultTemplates(): array
	{
		$templates = [];

		foreach ($this->queryBus->dispatch(FindProjectTemplatesQuery::create($this->projectId->toString())) as $projectTemplateView) {
			assert($projectTemplateView instanceof ProjectTemplateView);

			$templates[$projectTemplateView->templateLocale->value()] = $projectTemplateView->template->value();
		}

		return $templates;
	}
}
