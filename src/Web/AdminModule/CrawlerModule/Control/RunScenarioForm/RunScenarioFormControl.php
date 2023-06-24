<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm;

use Throwable;
use JsonException;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextArea;
use App\ReadModel\Category\CategoryView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\ReadModel\Category\AllCategoriesQuery;
use App\Application\Crawler\CrawlerClientProvider;
use App\ReadModel\Project\ProjectSelectOptionView;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenariosController;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;
use App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm\Event\ScenarioRunningEvent;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\RequestBody\ScenarioRequestBody;
use App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm\Event\FailedToRunScenarioEvent;

final class RunScenarioFormControl extends Control
{
	private FormFactoryInterface $formFactory;

	private QueryBusInterface $queryBus;

	private CrawlerClientProvider $crawlerClientProvider;

	private ValidLocalesProvider $validLocalesProvider;

	private ?ControllerResponseExceptionInterface $responseException = NULL;

	private string $projectUrl;

	public function __construct(
		FormFactoryInterface $formFactory,
		QueryBusInterface $queryBus,
		CrawlerClientProvider $crawlerClientProvider,
		ValidLocalesProvider $validLocalesProvider,
		string $projectUrl
	) {
		$this->formFactory = $formFactory;
		$this->queryBus = $queryBus;
		$this->crawlerClientProvider = $crawlerClientProvider;
		$this->validLocalesProvider = $validLocalesProvider;
		$this->projectUrl = $projectUrl;
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		assert($template instanceof RunScenarioFormTemplate);

		$template->responseException = $this->responseException;
	}

	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($translator);

		$form->addText('name', 'name.field')
			->setRequired('name.required')
			->addRule($form::MAX_LENGTH, 'name.rule_max_length', 255);

		$form->addSelect('project', 'project.field', $this->getProjectOptions())
			->setTranslator(NULL)
			->setOption('searchbar', TRUE)
			->setDefaultValue(NULL);

		[$categoryOptions, $necessaryCategories] = $this->getCategoryOptions();

		$form->addCheckboxList('categories', 'categories.field', $categoryOptions)
			->setTranslator(NULL)
			->setDefaultValue($necessaryCategories);

		$form->addTextArea('config', 'config.field', NULL, 4)
			->setRequired('config.required')
			->setOption('codemirror', 'json')
			->setOption('forceVerticalLayout', TRUE);

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('run', 'run.field');

		$form->onSuccess[] = function (Form $form): void {
			$this->runScenario($form);
		};

		return $form;
	}

	private function runScenario(Form $form): void
	{
		$values = $form->getValues();

		try {
			$config = json_decode($values->config, TRUE, 512, JSON_THROW_ON_ERROR);
		} catch (JsonException $e) {
			$configField = $form->getComponent('config');
			assert($configField instanceof TextArea);

			$configField->addError('config.error.invalid_json');
			$this->redrawControl();

			return;
		}

		$flags = [
			'projectId' => $values->project,
			'projectName' => $this->getProjectOptions()[$values->project] ?? 'unknown',
		];

		$categoryOptions = $this->getCategoryOptions()[0];

		foreach (array_keys($categoryOptions) as $categoryCode) {
			$enabled = in_array($categoryCode, $values->categories);
			$flags['category.' . $categoryCode] = $enabled ? '1' : '0';
		}

		$config['callbackUri'] = rtrim($this->projectUrl, '/') . '/api/crawler/receive-result';

		$requestBody = new ScenarioRequestBody(
			$values->name,
			$flags,
			$config,
		);

		try {
			$this->crawlerClientProvider->get()
				->getController(ScenariosController::class)
				->runScenario($requestBody);
		} catch (ControllerResponseExceptionInterface $e) {
			$this->responseException = $e;
			$this->dispatchEvent(new FailedToRunScenarioEvent($e));
			$this->redrawControl();

			return;
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new FailedToRunScenarioEvent($e));
			$this->redrawControl();

			return;
		}

		$this->dispatchEvent(new ScenarioRunningEvent());
		$this->redrawControl();
	}

	/**
	 * @return array<string, string>
	 */
	private function getProjectOptions(): array
	{
		$options = [];

		foreach ($this->queryBus->dispatch(FindProjectSelectOptionsQuery::all()) as $projectSelectOptionView) {
			assert($projectSelectOptionView instanceof ProjectSelectOptionView);
			$options += $projectSelectOptionView->toOption();
		}

		return $options;
	}

	/**
	 * @return array{0: array<string, string>, 1: array<int, string>}
	 */
	private function getCategoryOptions(): array
	{
		$categories = [];
		$necessary = [];
		$locale = $this->validLocalesProvider->getValidDefaultLocale();

		foreach ($this->queryBus->dispatch(AllCategoriesQuery::create()) as $categoryView) {
			assert($categoryView instanceof CategoryView);

			$categories[$categoryView->code->value()] = NULL !== $locale && isset($categoryView->names[$locale->code()]) ? $categoryView->names[$locale->code()]->value() : $categoryView->code->value();

			if ($categoryView->necessary) {
				$necessary[] = $categoryView->code->value();
			}
		}

		return [$categories, $necessary];
	}
}
