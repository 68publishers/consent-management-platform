<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm;

use Throwable;
use Nette\Utils\Html;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use App\ReadModel\Category\CategoryView;
use App\Domain\Category\ValueObject\Code;
use App\Domain\Category\ValueObject\Name;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\Command\CreateCategoryCommand;
use App\Domain\Category\Command\UpdateCategoryCommand;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\Category\Exception\CodeUniquenessException;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryFormProcessingFailedEvent;

final class CategoryFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private ValidLocalesProvider $validLocalesProvider;

	private ?CategoryView $default;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider             $validLocalesProvider
	 * @param \App\ReadModel\Category\CategoryView|NULL                        $default
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, ValidLocalesProvider $validLocalesProvider, ?CategoryView $default = NULL)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->validLocalesProvider = $validLocalesProvider;
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

		$form->addText('code', 'code.field')
			->setRequired('code.required')
			->addRule($form::MAX_LENGTH, 'code.rule_max_length', Code::MAX_LENGTH);

		$form->addCheckbox('active', 'active.field')
			->setDefaultValue(TRUE);

		$form->addCheckbox('necessary', 'necessary.field');

		$namesContainer = $form->addContainer('names');

		foreach ($this->validLocalesProvider->getValidLocales() as $locale) {
			$namesContainer->addText($locale->code(), Html::fromText($translator->translate('name.field', ['code' => $locale->code(), 'name' => $locale->name()])))
				->setRequired('name.required');
		}

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', NULL === $this->default ? 'save.field' : 'update.field');

		if (NULL !== $this->default) {
			$form->setDefaults([
				'code' => $this->default->code->value(),
				'active' => $this->default->active,
				'necessary' => $this->default->necessary,
				'names' => array_map(static fn (Name $name): string => $name->value(), $this->default->names),
			]);
		}

		$form->onSuccess[] = function (Form $form): void {
			$this->saveCategory($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveCategory(Form $form): void
	{
		$values = $form->values;

		if (NULL === $this->default) {
			$categoryId = CategoryId::new();
			$command = CreateCategoryCommand::create(
				$values->code,
				(array) $values->names,
				$values->active,
				$values->necessary,
				$categoryId->toString()
			);
		} else {
			$categoryId = $this->default->id;
			$command = UpdateCategoryCommand::create($categoryId->toString())
				->withCode($values->code)
				->withActive($values->active)
				->withNecessary($values->necessary)
				->withNames((array) $values->names);
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
			$this->dispatchEvent(new CategoryFormProcessingFailedEvent($e));

			return;
		}

		$this->dispatchEvent(NULL === $this->default ? new CategoryCreatedEvent($categoryId, $values->code) : new CategoryUpdatedEvent($categoryId, $this->default->code->value(), $values->code));
		$this->redrawControl();
	}
}
