<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use Closure;
use Throwable;
use Nette\Utils\Html;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use App\ReadModel\Cookie\CookieView;
use App\ReadModel\Category\CategoryView;
use App\Web\Ui\Form\FormFactoryInterface;
use Nepada\FormRenderer\TemplateRenderer;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\ReadModel\Category\AllCategoriesQuery;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\Cookie\Command\CreateCookieCommand;
use App\Domain\Cookie\Command\UpdateCookieCommand;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\Cookie\Exception\NameUniquenessException;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\ReadModel\CookieProvider\CookieProviderSelectOptionView;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\ReadModel\CookieProvider\FindCookieProviderSelectOptionsQuery;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;

final class CookieFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private ValidLocalesProvider $validLocalesProvider;

	private ?CookieProviderId $cookieProviderId = NULL;

	private ?CookieView $default;

	private ?array $providerOptions = NULL;

	private ?Closure $formProcessor = NULL;

	private ?array $overwrittenDefaults = NULL;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider             $validLocalesProvider
	 * @param \App\ReadModel\Cookie\CookieView|NULL                            $default
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, QueryBusInterface $queryBus, ValidLocalesProvider $validLocalesProvider, ?CookieView $default = NULL)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->validLocalesProvider = $validLocalesProvider;
		$this->default = $default;
	}

	public function setCookieProviderId(CookieProviderId $cookieProviderId): self
	{
		$this->cookieProviderId = $cookieProviderId;

		return $this;
	}

	public function setFormProcessor(Closure $formProcessor): void
	{
		$this->formProcessor = $formProcessor;
	}

	public function setOverwrittenDefaults(array $overwrittenDefaults): void
	{
		$this->overwrittenDefaults = $overwrittenDefaults;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();
		$providers = $this->getCookieProviders();
		$renderer = $form->getRenderer();
		assert($renderer instanceof TemplateRenderer);

		$form->setTranslator($translator);

		$renderer->importTemplate(__DIR__ . '/templates/form.imports.latte');
		$renderer->getTemplate()->providers = $providers;

		$form->addText('name', 'name.field')
			->setRequired('name.required');

		$form->addText('domain', 'domain.field');

		$providerField = $form->addSelect('provider', 'provider.field', array_map(static fn (CookieProviderSelectOptionView $view): string => $view->name->value(), $providers))
			->setPrompt('-------')
			->setRequired('provider.required')
			->setTranslator(NULL)
			->checkDefaultValue(FALSE)
			->setOption('searchbar', TRUE);

		$form->addSelect('category', 'category.field', $this->getCategories())
			->setPrompt('-------')
			->setRequired('category.required')
			->setTranslator(NULL)
			->checkDefaultValue(FALSE);

		$form->addRadioList('processing_time', 'processing_time.field')
			->setItems([ProcessingTime::PERSISTENT, ProcessingTime::SESSION, 'expiration'], FALSE)
			->setRequired('processing_time.required')
			->setDefaultValue(ProcessingTime::PERSISTENT)
			->addCondition($form::EQUAL, 'expiration')
				->toggle('#' . $this->getUniqueId() . '-processing_time_mask');

		$form->addText('processing_time_mask', 'processing_time_mask.field')
			->setOption('id', $this->getUniqueId() . '-processing_time_mask')
			->setOption('description', 'processing_time_mask.description')
			->addConditionOn($form->getComponent('processing_time'), $form::EQUAL, 'expiration')
				->setRequired('processing_time_mask.required')
				->addRule($form::PATTERN, 'processing_time_mask.rule_pattern', '(?:(?<years>\d+)y\s*)?(?:(?<months>\d+)m\s*)?(?:(?<days>\d+)d\s*)?(?:(?<hours>\d+)h\s*)?(?:(?<minutes>\d+)i\s*)?(?:(?<seconds>\d+)s\s*)?');

		$form->addCheckbox('active', 'active.field')
			->setDefaultValue(TRUE);

		$purposesContainer = $form->addContainer('purposes');

		foreach ($this->validLocalesProvider->getValidLocales() as $locale) {
			$purposesContainer->addTextArea($locale->code(), Html::fromText($translator->translate('purpose.field', ['code' => $locale->code(), 'name' => $locale->name()])), NULL, 4);
		}

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', NULL === $this->default ? 'save.field' : 'update.field');

		if (NULL !== $this->default) {
			$isExpiration = !in_array($this->default->processingTime->value(), [ProcessingTime::PERSISTENT, ProcessingTime::SESSION], TRUE);

			$form->setDefaults([
				'name' => $this->default->name->value(),
				'domain' => $this->default->domain->value(),
				'provider' => $providerDefaultValue = $this->default->cookieProviderId->toString(),
				'category' => $this->default->categoryId->toString(),
				'processing_time' => !$isExpiration ? $this->default->processingTime->value() : 'expiration',
				'processing_time_mask' => $isExpiration ? $this->default->processingTime->value() : '',
				'active' => $this->default->active,
				'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $this->default->purposes),
			]);
		} elseif (NULL !== $this->cookieProviderId) {
			$form->setDefaults([
				'provider' => $providerDefaultValue = $this->cookieProviderId->toString(),
			]);
		}

		if (NULL !== $this->overwrittenDefaults) {
			$form->setDefaults($this->overwrittenDefaults);
		}

		if (isset($providerDefaultValue) && array_key_exists($providerDefaultValue, $providers)) {
			$providerField->setDisabled(TRUE)
				->setOmitted(FALSE)
				->setDefaultValue($providerDefaultValue);
		}

		$form->onSuccess[] = function (Form $form): void {
			$this->saveCookie($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveCookie(Form $form): void
	{
		if (NULL !== $this->formProcessor) {
			($this->formProcessor)($form, $this);

			return;
		}

		$values = $form->values;

		if (NULL === $this->default) {
			$cookieId = CookieId::new();
			$command = CreateCookieCommand::create(
				$values->category,
				$values->provider,
				$values->name,
				$values->domain,
				'expiration' === $values->processing_time ? $values->processing_time_mask : $values->processing_time,
				$values->active,
				(array) $values->purposes,
				$cookieId->toString()
			);
		} else {
			$cookieId = $this->default->id;
			$command = UpdateCookieCommand::create($cookieId->toString())
				->withCategoryId($values->category)
				->withName($values->name)
				->withDomain($values->domain)
				->withProcessingTime('expiration' === $values->processing_time ? $values->processing_time_mask : $values->processing_time)
				->withActive($values->active)
				->withPurposes((array) $values->purposes);
		}

		try {
			$this->commandBus->dispatch($command);
		} catch (NameUniquenessException $e) {
			$nameField = $form->getComponent('name');
			assert($nameField instanceof TextInput);

			$nameField->addError('name.error.duplicated_value');

			return;
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new CookieFormProcessingFailedEvent($e));

			return;
		}

		$this->dispatchEvent(NULL === $this->default ? new CookieCreatedEvent($cookieId, $values->name) : new CookieUpdatedEvent($cookieId, $this->default->name->value(), $values->name));
		$this->redrawControl();
	}

	/**
	 * @return array
	 */
	private function getCategories(): array
	{
		$categories = [];
		$locale = $this->validLocalesProvider->getValidDefaultLocale();

		foreach ($this->queryBus->dispatch(AllCategoriesQuery::create()) as $categoryView) {
			assert($categoryView instanceof CategoryView);

			$categories[$categoryView->id->toString()] = NULL !== $locale && isset($categoryView->names[$locale->code()]) ? $categoryView->names[$locale->code()]->value() : $categoryView->code->value();
		}

		return $categories;
	}

	/**
	 * @return array
	 */
	private function getCookieProviders(): array
	{
		if (NULL !== $this->providerOptions) {
			return $this->providerOptions;
		}

		$options = [];
		$query = FindCookieProviderSelectOptionsQuery::all()
			->withPrivate(TRUE);

		foreach ($this->queryBus->dispatch($query) as $cookieProviderSelectOptionView) {
			assert($cookieProviderSelectOptionView instanceof CookieProviderSelectOptionView);

			$options[$cookieProviderSelectOptionView->id->toString()] = $cookieProviderSelectOptionView;
		}

		return $this->providerOptions = $options;
	}
}
