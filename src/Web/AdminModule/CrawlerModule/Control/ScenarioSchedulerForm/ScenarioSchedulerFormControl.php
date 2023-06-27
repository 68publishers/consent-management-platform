<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm;

use Throwable;
use JsonException;
use App\Web\Ui\Control;
use Nette\Utils\Strings;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextArea;
use App\ReadModel\Category\CategoryView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\ReadModel\Category\AllCategoriesQuery;
use App\Application\Crawler\CrawlerClientProvider;
use App\ReadModel\Project\ProjectSelectOptionView;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use App\Application\Crawler\CrawlerNotConfiguredException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulerResponse;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\ScenarioSchedulersController;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\ScenarioSchedulerCreatedEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\ScenarioSchedulerUpdatedEvent;
use SixtyEightPublishers\CrawlerClient\Controller\ScenarioScheduler\RequestBody\ScenarioSchedulerRequestBody;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\FailedToCreateScenarioSchedulerEvent;
use App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm\Event\FailedToUpdateScenarioSchedulerEvent;

final class ScenarioSchedulerFormControl extends Control
{
	private FormFactoryInterface $formFactory;

	private QueryBusInterface $queryBus;

	private CrawlerClientProvider $crawlerClientProvider;

	private ValidLocalesProvider $validLocalesProvider;

	private ?ControllerResponseExceptionInterface $responseException = NULL;

	private string $projectUrl;

	private ?ScenarioSchedulerResponse $scenarioSchedulerResponse;

	public function __construct(
		FormFactoryInterface $formFactory,
		QueryBusInterface $queryBus,
		CrawlerClientProvider $crawlerClientProvider,
		ValidLocalesProvider $validLocalesProvider,
		string $projectUrl,
		?ScenarioSchedulerResponse $scenarioSchedulerResponse = NULL
	) {
		$this->formFactory = $formFactory;
		$this->queryBus = $queryBus;
		$this->crawlerClientProvider = $crawlerClientProvider;
		$this->validLocalesProvider = $validLocalesProvider;
		$this->projectUrl = $projectUrl;
		$this->scenarioSchedulerResponse = $scenarioSchedulerResponse;
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		assert($template instanceof ScenarioSchedulerFormTemplate);

		$template->responseException = $this->responseException;
	}

	/**
	 * @throws CrawlerNotConfiguredException
	 */
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

		$form->addText('expression', 'expression.field')
			->setRequired('expression.required');

		$form->addSelect('project', 'project.field', $this->getProjectOptions())
			->checkDefaultValue(FALSE)
			->setRequired('project.required')
			->setPrompt('-----')
			->setTranslator(NULL)
			->setOption('searchbar', TRUE);

		$categoryOptions = $this->getCategoryOptions();

		$form->addCheckboxList('categories', 'categories.field', $categoryOptions)
			->checkDefaultValue(FALSE)
			->setTranslator(NULL);

		$form->addTextArea('config', 'config.field', NULL, 4)
			->setRequired('config.required')
			->setOption('codemirror', 'json')
			->setOption('forceVerticalLayout', TRUE);

		$form->addHidden('etag');
		$form->addProtection('//layout.form_protection');
		$form->addSubmit('save', NULL === $this->scenarioSchedulerResponse ? 'save.field' : 'update.field');

		if (NULL !== $this->scenarioSchedulerResponse) {
			$responseBody = $this->scenarioSchedulerResponse->getBody();
			$enabledCategories = [];

			foreach ($responseBody->flags as $flagName => $flagValue) {
				if (Strings::startsWith($flagName, 'category.') && '1' === $flagValue) {
					$enabledCategories[] = substr($flagName, 9);
				}
			}

			$form->setDefaults([
				'name' => $responseBody->name,
				'expression' => $responseBody->expression,
				'project' => $responseBody->flags['projectId'] ?? NULL,
				'categories' => $enabledCategories,
				'config' => $this->crawlerClientProvider->get()->getSerializer()->serialize($responseBody->config),
				'etag' => $this->scenarioSchedulerResponse->getEtag(),
			]);
		}

		$form->onSuccess[] = function (Form $form): void {
			$this->saveScenarioScheduler($form);
		};

		return $form;
	}

	private function saveScenarioScheduler(Form $form): void
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

		$categoryOptions = $this->getCategoryOptions();

		foreach (array_keys($categoryOptions) as $categoryCode) {
			$enabled = in_array($categoryCode, $values->categories);
			$flags['category.' . $categoryCode] = $enabled ? '1' : '0';
		}

		$config['callbackUri'] = rtrim($this->projectUrl, '/') . '/api/crawler/receive-result';

		$requestBody = new ScenarioSchedulerRequestBody(
			$values->name,
			$flags,
			$values->expression,
			$config,
		);

		try {
			$controller = $this->crawlerClientProvider->get()->getController(ScenarioSchedulersController::class);

			if (NULL === $this->scenarioSchedulerResponse) {
				$controller->createScenarioScheduler($requestBody);
			} else {
				$controller->updateScenarioScheduler($this->scenarioSchedulerResponse->getBody()->id, $values->etag, $requestBody);
			}
		} catch (ControllerResponseExceptionInterface $e) {
			$this->responseException = $e;
			$this->dispatchEvent(NULL === $this->scenarioSchedulerResponse ? new FailedToCreateScenarioSchedulerEvent($e) : new FailedToUpdateScenarioSchedulerEvent($e));
			$this->redrawControl();

			return;
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(NULL === $this->scenarioSchedulerResponse ? new FailedToCreateScenarioSchedulerEvent($e) : new FailedToUpdateScenarioSchedulerEvent($e));
			$this->redrawControl();

			return;
		}

		$this->dispatchEvent(NULL === $this->scenarioSchedulerResponse ? new ScenarioSchedulerCreatedEvent() : new ScenarioSchedulerUpdatedEvent());
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
	 * @return array<string, string>
	 */
	private function getCategoryOptions(): array
	{
		$categories = [];
		$locale = $this->validLocalesProvider->getValidDefaultLocale();

		foreach ($this->queryBus->dispatch(AllCategoriesQuery::create()) as $categoryView) {
			assert($categoryView instanceof CategoryView);
			$categories[$categoryView->code->value()] = NULL !== $locale && isset($categoryView->names[$locale->code()]) ? $categoryView->names[$locale->code()]->value() : $categoryView->code->value();
		}

		return $categories;
	}
}
