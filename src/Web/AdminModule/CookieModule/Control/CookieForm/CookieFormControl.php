<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use Throwable;
use Nette\Utils\Html;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\ReadModel\Cookie\CookieView;
use App\ReadModel\Category\CategoryView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\ReadModel\Category\AllCategoriesQuery;
use App\Domain\Cookie\Command\CreateCookieCommand;
use App\Domain\Cookie\Command\UpdateCookieCommand;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;

final class CookieFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private CookieProviderId $cookieProviderId;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private ValidLocalesProvider $validLocalesProvider;

	private ?CookieView $default;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId          $cookieProviderId
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider             $validLocalesProvider
	 * @param \App\ReadModel\Cookie\CookieView|NULL                            $default
	 */
	public function __construct(CookieProviderId $cookieProviderId, FormFactoryInterface $formFactory, CommandBusInterface $commandBus, QueryBusInterface $queryBus, ValidLocalesProvider $validLocalesProvider, ?CookieView $default = NULL)
	{
		$this->cookieProviderId = $cookieProviderId;
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
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

		$form->addText('name', 'name.field')
			->setRequired('name.required');

		$form->addSelect('category', 'category.field', $this->getCategories())
			->setPrompt('-------')
			->setRequired('category.required')
			->setTranslator(NULL)
			->checkDefaultValue(FALSE);

		$translationsContainer = $form->addContainer('translations');

		foreach ($this->validLocalesProvider->getValidLocales() as $locale) {
			$localeContainer = $translationsContainer->addContainer($locale->code());

			$localeContainer->addTextArea('purpose', Html::fromText($translator->translate('purpose.field', ['code' => $locale->code(), 'name' => $locale->name()])));
			$localeContainer->addText('processing_time', Html::fromText($translator->translate('processing_time.field', ['code' => $locale->code(), 'name' => $locale->name()])));
		}

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', NULL === $this->default ? 'save.field' : 'update.field');

		if (NULL !== $this->default) {
			$translations = array_map(static fn (Purpose $purpose): array => ['purpose' => $purpose->value()], $this->default->purposes);

			foreach ($this->default->processingTimes as $locale => $processingTime) {
				$translations[$locale]['processing_time'] = $processingTime->value();
			}

			$form->setDefaults([
				'name' => $this->default->name->value(),
				'category' => $this->default->categoryId->toString(),
				'translations' => $translations,
			]);
		}

		$form->onSuccess[] = function (Form $form) {
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
		$values = $form->values;
		$purposes = [];
		$processingTimes = [];

		foreach ($values->translations as $locale => $translations) {
			$purposes[$locale] = $translations->purpose;
			$processingTimes[$locale] = $translations->processing_time;
		}

		if (NULL === $this->default) {
			$cookieId = CookieId::new();
			$command = CreateCookieCommand::create(
				$values->category,
				$this->cookieProviderId->toString(),
				$values->name,
				$purposes,
				$processingTimes,
				$cookieId->toString()
			);
		} else {
			$cookieId = $this->default->id;
			$command = UpdateCookieCommand::create($cookieId->toString())
				->withCategoryId($values->category)
				->withName($values->name)
				->withPurposes($purposes)
				->withProcessingTimes($processingTimes);
		}

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new ProviderFormProcessingFailedEvent($e));

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
}
