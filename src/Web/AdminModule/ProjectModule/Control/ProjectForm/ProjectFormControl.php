<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use App\ReadModel\Project\ProjectView;
use App\Domain\Project\ValueObject\Code;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\Command\CreateProjectCommand;
use App\Domain\Project\Command\UpdateProjectCommand;
use App\Domain\Project\Exception\CodeUniquenessException;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectCreatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event\ProjectFormProcessingFailedEvent;

final class ProjectFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private ?ProjectView $default;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\ReadModel\Project\ProjectView|NULL                          $default
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, ?ProjectView $default = NULL)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->default = $default;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($translator);

		$form->addText('name', 'name.field')
			->setRequired('name.required');

		$form->addText('code', 'code.field')
			->setRequired('code.required')
			->addRule($form::PATTERN, 'code.rule_pattern', '[a-z0-9_\-\.]+')
			->addRule($form::MAX_LENGTH, 'code.rule_max_length', Code::MAX_LENGTH)
			->setOption('description', 'code.description');

		$form->addText('color', 'color.field')
			->setRequired('color.required')
			->addRule($form::PATTERN, 'color.rule_pattern', '#([a-fA-F0-9]{3}){1,2}\b')
			->setOption('description', 'color.description');

		$form->addCheckbox('active', 'active.field');

		$form->addTextArea('description', 'description.field', NULL, 4);

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', NULL === $this->default ? 'save.field' : 'update.field');

		if (NULL !== $this->default) {
			$form->setDefaults([
				'name' => $this->default->name->value(),
				'code' => $this->default->code->value(),
				'color' => $this->default->color->value(),
				'active' => $this->default->active,
				'description' => $this->default->description->value(),
			]);
		}

		$form->onSuccess[] = function (Form $form) {
			$this->saveProject($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveProject(Form $form): void
	{
		$values = $form->values;

		if (NULL === $this->default) {
			$projectId = ProjectId::new();
			$command = CreateProjectCommand::create(
				$values->name,
				$values->code,
				$values->description,
				$values->color,
				$values->active,
				$projectId->toString()
			);
		} else {
			$projectId = $this->default->id;
			$command = UpdateProjectCommand::create($projectId->toString())
				->withName($values->name)
				->withCode($values->code)
				->withDescription($values->description)
				->withColor($values->color)
				->withActive($values->active);
		}

		try {
			$this->commandBus->dispatch($command);
		} catch (CodeUniquenessException $e) {
			$emailAddressField = $form->getComponent('code');
			assert($emailAddressField instanceof TextInput);

			$emailAddressField->addError('code.error.duplicated_value');

			return;
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new ProjectFormProcessingFailedEvent($e));

			return;
		}

		$this->dispatchEvent(NULL === $this->default ? new ProjectCreatedEvent($projectId, $values->code) : new ProjectUpdatedEvent($projectId, $this->default->code->value(), $values->code));
		$this->redrawControl();
	}
}
