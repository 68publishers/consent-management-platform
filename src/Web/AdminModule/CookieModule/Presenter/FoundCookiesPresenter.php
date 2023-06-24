<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use Throwable;
use DomainException;
use Psr\Log\LoggerInterface;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use App\ReadModel\Cookie\CookieView;
use Nette\Application\AbortException;
use App\ReadModel\Project\ProjectView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Domain\Cookie\ValueObject\Purpose;
use Nette\Application\BadRequestException;
use App\ReadModel\Cookie\GetCookieByIdQuery;
use App\Application\Acl\FoundCookiesResource;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Project\GetProjectByIdQuery;
use App\ReadModel\Project\FindAllProjectsQuery;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Domain\Cookie\Command\CreateCookieCommand;
use App\Domain\Cookie\Command\UpdateCookieCommand;
use App\ReadModel\CookieSuggestion\CookieSuggestion;
use App\Domain\Cookie\Exception\NameUniquenessException;
use App\ReadModel\CookieSuggestion\GetCookieSuggestionByIdQuery;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Domain\Project\Command\AddCookieProvidersToProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Application\CookieSuggestion\CookieSuggestionsStoreInterface;
use App\Application\CookieSuggestion\Suggestion\IgnoredCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnproblematicCookieSuggestion;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Domain\CookieSuggestion\Command\IgnoreCookieSuggestionUntilNextOccurrenceCommand;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;

/**
 * @IsAllowed(resource=FoundCookiesResource::class, privilege=FoundCookiesResource::READ)
 */
final class FoundCookiesPresenter extends AdminPresenter
{
	/** @persistent  */
	public ?array $solution = NULL;

	private QueryBusInterface $queryBus;

	private CommandBusInterface $commandBus;

	private CookieSuggestionsStoreInterface $cookieSuggestionsStore;

	private LoggerInterface $logger;

	private CookieFormModalControlFactoryInterface $cookieFormModalControlFactory;

	private ProjectView $projectView;

	public function __construct(
		QueryBusInterface $queryBus,
		CommandBusInterface $commandBus,
		CookieSuggestionsStoreInterface $cookieSuggestionsStore,
		LoggerInterface $logger,
		CookieFormModalControlFactoryInterface $cookieFormModalControlFactory
	) {
		parent::__construct();

		$this->queryBus = $queryBus;
		$this->commandBus = $commandBus;
		$this->cookieSuggestionsStore = $cookieSuggestionsStore;
		$this->logger = $logger;
		$this->cookieFormModalControlFactory = $cookieFormModalControlFactory;
	}

	/**
	 * @throws AbortException
	 */
	public function actionDefault(string $id): void
	{
		$projectView = ProjectId::isValid($id) ? $this->queryBus->dispatch(GetProjectByIdQuery::create($id)) : NULL;

		if (!$projectView instanceof ProjectView) {
			$this->subscribeFlashMessage(FlashMessage::warning('project_not_found'));
			$this->redirect('FoundCookiesProjects:');
		}

		$this->projectView = $projectView;
	}

	/**
	 * @param array<string, mixed> $args
	 *
	 * @throws AbortException
	 */
	public function handleSolution(string $cookieSuggestionId, string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, array $args): void
	{
		$this->solution = [
			'cookieSuggestionId' => $cookieSuggestionId,
			'solutionsUniqueId' => $solutionsUniqueId,
			'solutionUniqueId' => $solutionUniqueId,
			'solutionType' => $solutionType,
			'args' => $args,
		];

		$this->processSolution();
	}

	/**
	 * @param array<string, mixed> $values
	 *
	 * @throws AbortException
	 */
	public function handleResolve(string $cookieSuggestionId, string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, array $values): void
	{
		$this->resolveSolution($cookieSuggestionId, $solutionsUniqueId, $solutionUniqueId, $solutionType, $values);
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();

		$template = $this->getTemplate();
		assert($template instanceof FoundCookiesTemplate);

		$suggestionsResult = $this->cookieSuggestionsStore->resolveCookieSuggestions($this->projectView->id->toString());

		$template->projectView = $this->projectView;
		$template->allProjects = $this->queryBus->dispatch(FindAllProjectsQuery::create());
		$template->missingCookieSuggestions = $suggestionsResult->getSuggestions(MissingCookieSuggestion::class);
		$template->unassociatedCookieSuggestions = $suggestionsResult->getSuggestions(UnassociatedCookieSuggestion::class);
		$template->problematicCookieSuggestions = $suggestionsResult->getSuggestions(ProblematicCookieSuggestion::class);
		$template->unproblematicCookieSuggestions = $suggestionsResult->getSuggestions(UnproblematicCookieSuggestion::class);
		$template->ignoredCookieSuggestions = $suggestionsResult->getSuggestions(IgnoredCookieSuggestion::class);
	}

	/**
	 * @throws BadRequestException
	 */
	protected function createComponentCookieFormModal(): CookieFormModalControl
	{
		$solutionType = $this->solution['solutionType'] ?? '';

		if (!in_array($solutionType, ['change_cookie_category', 'create_new_cookie', 'create_new_cookie_with_not_accepted_category'])) {
			$this->error(sprintf(
				'Unable to launch the cookie modal for solution of the type "%s".',
				$solutionType,
			));
		}

		$cookieSuggestion = $this->queryBus->dispatch(GetCookieSuggestionByIdQuery::create($this->solution['cookieSuggestionId']));

		if (!$cookieSuggestion instanceof CookieSuggestion) {
			$this->error(sprintf(
				'Cookies suggestion "%s" not found.',
				$this->solution['cookieSuggestionId'],
			));
		}

		$cookieView = isset($this->solution['args']['existing_cookie_id']) ? $this->queryBus->dispatch(GetCookieByIdQuery::create($this->solution['args']['existing_cookie_id'])) : NULL;

		$control = $this->cookieFormModalControlFactory->create($this->validLocalesProvider, 'change_cookie_category' === $solutionType ? $cookieView : NULL);
		$inner = $control->getInnerControl();

		$inner->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$solutionsData = $this->cookieSuggestionsStore->getDataStore()->get($this->solution['solutionsUniqueId']);

		if (is_array($solutionsData)
			&& ($solutionsData['solutionUniqueId'] ?? '') === $this->solution['solutionUniqueId']
			&& ($solutionsData['solutionType'] ?? '') === $this->solution['solutionType']
			&& isset($solutionsData['values'], $solutionsData['values']['form_values'])
		) {
			$inner->setOverwrittenDefaults($solutionsData['values']['form_values']);
		} elseif ('create_new_cookie' === $solutionType) {
			$inner->setOverwrittenDefaults([
				'name' => $cookieSuggestion->name,
				'domain' => $cookieSuggestion->domain,
			]);
		} elseif ('create_new_cookie_with_not_accepted_category' === $solutionType && $cookieView instanceof CookieView) {
			$isExpiration = !in_array($cookieView->processingTime->value(), [ProcessingTime::PERSISTENT, ProcessingTime::SESSION], TRUE);

			$inner->setOverwrittenDefaults([
				'name' => $cookieView->name->value(),
				'domain' => $cookieView->domain->value() ?: $cookieSuggestion->domain,
				'provider' => $cookieView->cookieProviderId->toString(),
				'processing_time' => !$isExpiration ? $cookieView->processingTime->value() : 'expiration',
				'processing_time_mask' => $isExpiration ? $cookieView->processingTime->value() : '',
				'active' => $cookieView->active,
				'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $cookieView->purposes),
			]);
		} elseif ('change_cookie_category' === $solutionType) {
			$defaults = [
				'category' => NULL,
			];

			if (!$cookieView instanceof CookieView || empty($cookieView->domain->value())) {
				$defaults['domain'] = $cookieSuggestion->domain;
			}

			$inner->setOverwrittenDefaults($defaults);
		}

		$inner->setFormProcessor(function (Form $form) use ($cookieSuggestion, $solutionType, $cookieView): void {
			$this->storeSolutionValues($this->solution, [
				'cookie_suggestion_id' => $cookieSuggestion->id,
				'existing_cookie_id' => 'change_cookie_category' === $solutionType && $cookieView instanceof CookieView ? $cookieView->id->toString() : NULL,
				'form_values' => $form->getValues('array'),
			]);
			$this->closeModal();
			$this->redrawControl();
			$this->redirectIfNotAjax();
		});

		return $control;
	}

	/**
	 * @throws AbortException
	 */
	private function processSolution(): void
	{
		if (NULL === $this->solution) {
			$this->subscribeFlashMessage(FlashMessage::error('unable_to_process_solution'));
			$this->redrawControl();
			$this->redirectIfNotAjax();

			return;
		}

		switch ($this->solution['solutionType']) {
			case 'ignore_until_next_occurrence':
				$this->storeSolutionValues($this->solution, []);
				$this->redrawControl();
				$this->redirectIfNotAjax();

				break;
			case 'associate_cookie_provider_with_project':
				$this->storeSolutionValues($this->solution, [
					'provider_id' => $this->solution['args']['provider_id'],
				]);
				$this->redrawControl();
				$this->redirectIfNotAjax();

				break;
			case 'change_cookie_category':
			case 'create_new_cookie':
			case 'create_new_cookie_with_not_accepted_category':
				$this->handleOpenModal('cookieFormModal');

				break;
			default:
				$this->subscribeFlashMessage(FlashMessage::error('unable_to_process_solution'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
		}
	}

	private function storeSolutionValues(array $solution, array $values): void
	{
		$this->cookieSuggestionsStore->getDataStore()->store(
			$solution['solutionsUniqueId'],
			$solution['solutionUniqueId'],
			$solution['solutionType'],
			$values,
		);
		$this->solution = NULL;
	}

	/**
	 * @throws AbortException
	 */
	private function resolveSolution(string $cookieSuggestionId, string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, array $values): void
	{
		switch ($solutionType) {
			case 'ignore_until_next_occurrence':
				$this->resolveIgnoreUntilNextOccurrence($cookieSuggestionId, $solutionsUniqueId);

				break;
			case 'associate_cookie_provider_with_project':
				$this->resolveAssociateCookieProviderWithProject($values['provider_id'], $solutionsUniqueId);

				break;
			case 'change_cookie_category':
			case 'create_new_cookie':
			case 'create_new_cookie_with_not_accepted_category':
				$this->resolveCookieForm(
					$values['cookie_suggestion_id'],
					$values['existing_cookie_id'] ?? NULL,
					$values['form_values'],
					$solutionsUniqueId,
					$solutionUniqueId,
					$solutionType,
				);

				break;
			default:
				$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
				$this->redrawControl();
		}
	}

	/**
	 * @throws AbortException
	 */
	private function resolveIgnoreUntilNextOccurrence(string $cookieSuggestionId, string $solutionsUniqueId): void
	{
		try {
			$this->commandBus->dispatch(IgnoreCookieSuggestionUntilNextOccurrenceCommand::create($cookieSuggestionId));
			$this->subscribeFlashMessage(FlashMessage::success('suggestion_resolved'));

			$this->cookieSuggestionsStore->getDataStore()->remove($solutionsUniqueId);
			$this->solution = NULL;
		} catch (Throwable $e) {
			if (!$e instanceof DomainException) {
				$this->logger->error((string) $e);
			}

			$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
		}

		$this->redrawControl();
		$this->redirectIfNotAjax();
	}

	/**
	 * @throws AbortException
	 */
	private function resolveAssociateCookieProviderWithProject(string $cookieProviderId, string $solutionsUniqueId): void
	{
		try {
			$this->commandBus->dispatch(AddCookieProvidersToProjectCommand::create(
				$this->projectView->id->toString(),
				$cookieProviderId,
			));
			$this->subscribeFlashMessage(FlashMessage::success('suggestion_resolved'));

			$this->cookieSuggestionsStore->getDataStore()->remove($solutionsUniqueId);
			$this->solution = NULL;
		} catch (Throwable $e) {
			if (!$e instanceof DomainException) {
				$this->logger->error((string) $e);
			}

			$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
		}

		$this->redrawControl();
		$this->redirectIfNotAjax();
	}

	/**
	 * @throws AbortException
	 */
	private function resolveCookieForm(string $cookieSuggestionId, ?string $existingCookieId, array $formValues, string $solutionsUniqueId, string $solutionUniqueId, string $solutionType): void
	{
		try {
			$command = NULL === $existingCookieId
				? CreateCookieCommand::create(
					$formValues['category'],
					$formValues['provider'],
					$formValues['name'],
					$formValues['domain'],
					'expiration' === $formValues['processing_time'] ? $formValues['processing_time_mask'] : $formValues['processing_time'],
					(bool) $formValues['active'],
					$formValues['purposes'],
				)
				: UpdateCookieCommand::create($existingCookieId)
					->withCategoryId($formValues['category'])
					->withName($formValues['name'])
					->withDomain($formValues['domain'])
					->withProcessingTime('expiration' === $formValues['processing_time'] ? $formValues['processing_time_mask'] : $formValues['processing_time'])
					->withActive((bool) $formValues['active'])
					->withPurposes($formValues['purposes']);

			$this->commandBus->dispatch($command);

			$this->cookieSuggestionsStore->getDataStore()->remove($solutionsUniqueId);
			$this->subscribeFlashMessage(FlashMessage::success('suggestion_resolved'));
			$this->solution = NULL;
		} catch (NameUniquenessException $e) {
			$this->solution = [
				'cookieSuggestionId' => $cookieSuggestionId,
				'solutionsUniqueId' => $solutionsUniqueId,
				'solutionUniqueId' => $solutionUniqueId,
				'solutionType' => $solutionType,
				'args' => [
					'existing_cookie_id' => $existingCookieId,
				],
			];

			$modal = $this->getComponent('cookieFormModal');
			$inner = $modal->getInnerControl();
			$form = $inner->getComponent('form');

			$nameField = $form->getComponent('name');
			assert($nameField instanceof TextInput);

			$nameField->addError('name.error.duplicated_value');

			$this->processSolution();
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
		}

		$this->redrawControl();
		$this->redirectIfNotAjax();
	}
}
