<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use Throwable;
use DomainException;
use Psr\Log\LoggerInterface;
use Nette\Application\UI\Form;
use Nette\InvalidStateException;
use Nette\Forms\Controls\TextInput;
use App\ReadModel\Cookie\CookieView;
use Nette\Application\UI\Multiplier;
use Nette\Application\AbortException;
use App\ReadModel\Project\ProjectView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Domain\Cookie\ValueObject\Purpose;
use Nette\Application\BadRequestException;
use App\Domain\Cookie\ValueObject\CookieId;
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
use SixtyEightPublishers\FlashMessageBundle\Domain\Phrase;
use App\ReadModel\CookieSuggestion\GetCookieSuggestionByIdQuery;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Domain\Project\Command\AddCookieProvidersToProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Application\CookieSuggestion\CookieSuggestionsStoreInterface;
use App\Application\CookieSuggestion\Suggestion\IgnoredCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\Domain\CookieSuggestion\Command\DoNotIgnoreCookieSuggestionCommand;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use SixtyEightPublishers\UserBundle\Application\Exception\IdentityException;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnproblematicCookieSuggestion;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Domain\CookieSuggestion\Command\IgnoreCookieSuggestionPermanentlyCommand;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieUpdatedEvent;
use App\Domain\CookieSuggestion\Command\IgnoreCookieSuggestionUntilNextOccurrenceCommand;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
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
	 * @throws IdentityException
	 * @throws BadRequestException
	 */
	protected function startup(): void
	{
		parent::startup();

		if (!$this->globalSettings->crawlerSettings()->enabled()) {
			$this->error('Crawler is disabled.');
		}
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
	public function handleSolution(
		string $cookieSuggestionId,
		string $solutionsUniqueId,
		string $solutionUniqueId,
		string $solutionType,
		array $args
	): void {
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
	public function handleResolve(
		string $cookieSuggestionId,
		string $solutionsUniqueId,
		string $solutionUniqueId,
		string $solutionType,
		array $values
	): void {
		$this->resolveSolution($cookieSuggestionId, $solutionsUniqueId, $solutionUniqueId, $solutionType, $values);
	}

	/**
	 * @throws AbortException
	 */
	public function handleResolveAll(): void
	{
		$success = 0;
		$error = 0;

		foreach ($this->cookieSuggestionsStore->getDataStore()->getAll($this->projectView->id->toString()) as $solutionsUniqueId => $state) {
			$resolved = $this->resolveSolution($state['cookieSuggestionId'], $solutionsUniqueId, $state['solutionUniqueId'], $state['solutionType'], $state['values'] ?? [], FALSE);

			if ($resolved) {
				++$success;
			} else {
				++$error;
			}
		}

		if (0 < $success) {
			$this->subscribeFlashMessage(FlashMessage::success(Phrase::create('multiple_suggestions_resolved', $success)));
		}

		if (0 < $error) {
			$this->subscribeFlashMessage(FlashMessage::error(Phrase::create('unable_to_resolve_multiple_solutions', $error)));
		}

		$this->redrawControl();
		$this->redirectIfNotAjax();
	}

	/**
	 * @throws AbortException
	 */
	public function handleResetSolution(string $solutionsUniqueId): void
	{
		$this->cookieSuggestionsStore->getDataStore()->remove($this->projectView->id->toString(), $solutionsUniqueId);
		$this->redrawControl();
		$this->redirectIfNotAjax();
	}

	/**
	 * @throws AbortException
	 */
	public function handleResetAll(): void
	{
		$this->cookieSuggestionsStore->getDataStore()->removeAll($this->projectView->id->toString());
		$this->redrawControl();
		$this->redirectIfNotAjax();
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

		$template->totalNumberOfResolvableSuggestions = $suggestionsResult->getTotalNumberOfResolvableSuggestions();
		$template->totalNumberOfReadyToResolveSuggestions = count($this->cookieSuggestionsStore->getDataStore()->getAll($this->projectView->id->toString()));
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

		$solutionsData = $this->cookieSuggestionsStore->getDataStore()->get(
			$this->projectView->id->toString(),
			$this->solution['solutionsUniqueId'],
		);

		if (is_array($solutionsData)
			&& ($solutionsData['solutionUniqueId'] ?? '') === $this->solution['solutionUniqueId']
			&& ($solutionsData['solutionType'] ?? '') === $this->solution['solutionType']
			&& isset($solutionsData['values'], $solutionsData['values']['form_values'])
		) {
			$inner->setOverwrittenDefaults($solutionsData['values']['form_values']);
		} elseif ('create_new_cookie' === $solutionType) {
			$defaultProviderId = NULL;

			foreach ($inner->getCookieProviders() as $cookieProviderId => $cookieProviderOption) {
				$providerCode = $cookieProviderOption->code->value();

				if ($providerCode === substr($cookieSuggestion->domain, -strlen($providerCode))) {
					$defaultProviderId = $cookieProviderId;

					break;
				}
			}

			$inner->setOverwrittenDefaults([
				'name' => $cookieSuggestion->name,
				'domain' => $cookieSuggestion->domain,
				'provider' => $defaultProviderId,
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

	protected function createComponentEditCookieModal(): Multiplier
	{
		return new Multiplier(function (string $id): CookieFormModalControl {
			$cookieId = CookieId::fromString($id);
			$cookieView = $this->queryBus->dispatch(GetCookieByIdQuery::create($cookieId->toString()));

			if (!$cookieView instanceof CookieView) {
				throw new InvalidStateException('Cookie not found.');
			}

			$control = $this->cookieFormModalControlFactory->create($this->validLocalesProvider, $cookieView);
			$inner = $control->getInnerControl();

			$inner->setFormFactoryOptions([
				FormFactoryInterface::OPTION_AJAX => TRUE,
			]);

			$inner->addEventListener(CookieUpdatedEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::success('cookie_updated'));
				$this->redrawControl();
				$this->closeModal();
			});

			$inner->addEventListener(CookieFormProcessingFailedEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::error('cookie_update_failed'));
			});

			return $control;
		});
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
			case 'ignore_permanently':
			case 'do_not_ignore':
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
			$this->projectView->id->toString(),
			$solution['solutionsUniqueId'],
			$solution['solutionUniqueId'],
			$solution['solutionType'],
			$solution['cookieSuggestionId'],
			$values,
		);
		$this->solution = NULL;
	}

	/**
	 * @throws AbortException
	 */
	private function resolveSolution(
		string $cookieSuggestionId,
		string $solutionsUniqueId,
		string $solutionUniqueId,
		string $solutionType,
		array $values,
		bool $uiActionsAllowed = TRUE
	): bool {
		switch ($solutionType) {
			case 'ignore_until_next_occurrence':
				return $this->resolveIgnore($cookieSuggestionId, $solutionsUniqueId, FALSE, $uiActionsAllowed);
			case 'ignore_permanently':
				return $this->resolveIgnore($cookieSuggestionId, $solutionsUniqueId, TRUE, $uiActionsAllowed);
			case 'do_not_ignore':
				return $this->resolveDoNotIgnore($cookieSuggestionId, $solutionsUniqueId, $uiActionsAllowed);
			case 'associate_cookie_provider_with_project':
				return $this->resolveAssociateCookieProviderWithProject($values['provider_id'], $solutionsUniqueId, $uiActionsAllowed);
			case 'change_cookie_category':
			case 'create_new_cookie':
			case 'create_new_cookie_with_not_accepted_category':
				return $this->resolveCookieForm(
					$values['cookie_suggestion_id'],
					$values['existing_cookie_id'] ?? NULL,
					$values['form_values'],
					$solutionsUniqueId,
					$solutionUniqueId,
					$solutionType,
					$uiActionsAllowed,
				);
			default:
				if ($uiActionsAllowed) {
					$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
					$this->redrawControl();
				}

				return FALSE;
		}
	}

	/**
	 * @throws AbortException
	 */
	private function resolveIgnore(
		string $cookieSuggestionId,
		string $solutionsUniqueId,
		bool $permanently,
		bool $uiActionsAllowed
	): bool {
		try {
			$this->commandBus->dispatch(
				$permanently
				? IgnoreCookieSuggestionPermanentlyCommand::create($cookieSuggestionId)
				: IgnoreCookieSuggestionUntilNextOccurrenceCommand::create($cookieSuggestionId)
			);

			$this->cookieSuggestionsStore->getDataStore()->remove($this->projectView->id->toString(), $solutionsUniqueId);
			$this->solution = NULL;

			if ($uiActionsAllowed) {
				$this->subscribeFlashMessage(FlashMessage::success('suggestion_resolved'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
			}

			return TRUE;
		} catch (AbortException $e) {
			throw $e;
		} catch (Throwable $e) {
			if (!$e instanceof DomainException) {
				$this->logger->error((string) $e);
			}

			if ($uiActionsAllowed) {
				$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
			}

			return FALSE;
		}
	}

	/**
	 * @throws AbortException
	 */
	private function resolveDoNotIgnore(
		string $cookieSuggestionId,
		string $solutionsUniqueId,
		bool $uiActionsAllowed
	): bool {
		try {
			$this->commandBus->dispatch(DoNotIgnoreCookieSuggestionCommand::create($cookieSuggestionId));

			$this->cookieSuggestionsStore->getDataStore()->remove($this->projectView->id->toString(), $solutionsUniqueId);
			$this->solution = NULL;

			if ($uiActionsAllowed) {
				$this->subscribeFlashMessage(FlashMessage::success('cookie_is_no_longer_ignored'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
			}

			return TRUE;
		} catch (AbortException $e) {
			throw $e;
		} catch (Throwable $e) {
			if (!$e instanceof DomainException) {
				$this->logger->error((string) $e);
			}

			if ($uiActionsAllowed) {
				$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
			}

			return FALSE;
		}
	}

	/**
	 * @throws AbortException
	 */
	private function resolveAssociateCookieProviderWithProject(
		string $cookieProviderId,
		string $solutionsUniqueId,
		bool $uiActionsAllowed
	): bool {
		try {
			$this->commandBus->dispatch(AddCookieProvidersToProjectCommand::create(
				$this->projectView->id->toString(),
				$cookieProviderId,
			));

			$this->cookieSuggestionsStore->getDataStore()->remove($this->projectView->id->toString(), $solutionsUniqueId);
			$this->solution = NULL;

			if ($uiActionsAllowed) {
				$this->subscribeFlashMessage(FlashMessage::success('suggestion_resolved'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
			}

			return TRUE;
		} catch (AbortException $e) {
			throw $e;
		} catch (Throwable $e) {
			if (!$e instanceof DomainException) {
				$this->logger->error((string) $e);
			}

			if ($uiActionsAllowed) {
				$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
			}

			return FALSE;
		}
	}

	/**
	 * @throws AbortException
	 */
	private function resolveCookieForm(
		string $cookieSuggestionId,
		?string $existingCookieId,
		array $formValues,
		string $solutionsUniqueId,
		string $solutionUniqueId,
		string $solutionType,
		bool $uiActionsAllowed
	): bool {
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

			$this->cookieSuggestionsStore->getDataStore()->remove($this->projectView->id->toString(), $solutionsUniqueId);
			$this->solution = NULL;

			if ($uiActionsAllowed) {
				$this->subscribeFlashMessage(FlashMessage::success('suggestion_resolved'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
			}

			return TRUE;
		} catch (NameUniquenessException $e) {
			if (!$uiActionsAllowed) {
				return FALSE;
			}

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

			return FALSE;
		} catch (AbortException $e) {
			throw $e;
		} catch (Throwable $e) {
			$this->logger->error((string) $e);

			if ($uiActionsAllowed) {
				$this->subscribeFlashMessage(FlashMessage::error('unable_to_resolve_solution'));
				$this->redrawControl();
				$this->redirectIfNotAjax();
			}

			return FALSE;
		}
	}
}
