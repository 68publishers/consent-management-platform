<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderForm;

use Throwable;
use Nette\Utils\Html;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\Purpose;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\Exception\CodeUniquenessException;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use App\Domain\CookieProvider\Command\UpdateCookieProviderCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;

final class ProviderFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private ValidLocalesProvider $validLocalesProvider;

	private ?CookieProviderView $default;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider             $validLocalesProvider
	 * @param \App\ReadModel\CookieProvider\CookieProviderView|NULL            $default
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, ValidLocalesProvider $validLocalesProvider, ?CookieProviderView $default = NULL)
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

		$form->addText('name', 'name.field')
			->setRequired('name.required');

		$form->addRadioList('type', 'type.field')
			->setItems(ProviderType::values(), FALSE)
			->setRequired('type.required')
			->setDefaultValue(ProviderType::THIRD_PARTY);

		$form->addText('link', 'link.field')
			->setRequired('link.required')
			->addRule($form::URL, 'link.rule_url');

		$namesContainer = $form->addContainer('purposes');

		foreach ($this->validLocalesProvider->getValidLocales() as $locale) {
			$namesContainer->addTextArea($locale->code(), Html::fromText($translator->translate('purpose.field', ['code' => $locale->code(), 'name' => $locale->name()])), NULL, 4);
		}

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', NULL === $this->default ? 'save.field' : 'update.field');

		if (NULL !== $this->default) {
			$form->setDefaults([
				'code' => $this->default->code->value(),
				'name' => $this->default->name->value(),
				'type' => $this->default->type->value(),
				'link' => $this->default->link->value(),
				'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $this->default->purposes),
			]);
		}

		$form->onSuccess[] = function (Form $form) {
			$this->saveProvider($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveProvider(Form $form): void
	{
		$values = $form->values;

		if (NULL === $this->default) {
			$cookieProviderId = CookieProviderId::new();
			$command = CreateCookieProviderCommand::create(
				$values->code,
				$values->type,
				$values->name,
				$values->link,
				(array) $values->purposes,
				$cookieProviderId->toString()
			);
		} else {
			$cookieProviderId = $this->default->id;
			$command = UpdateCookieProviderCommand::create($cookieProviderId->toString())
				->withCode($values->code)
				->withType($values->type)
				->withName($values->name)
				->withLink($values->link)
				->withPurposes((array) $values->purposes);
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
			$this->dispatchEvent(new ProviderFormProcessingFailedEvent($e));

			return;
		}

		$this->dispatchEvent(NULL === $this->default ? new ProviderCreatedEvent($cookieProviderId, $values->code) : new ProviderUpdatedEvent($cookieProviderId, $this->default->code->value(), $values->code));
		$this->redrawControl();
	}
}
