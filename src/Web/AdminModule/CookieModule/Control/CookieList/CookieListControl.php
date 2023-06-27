<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieList;

use App\Web\Ui\Control;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use App\ReadModel\Cookie\CookieView;
use Nette\Application\UI\Multiplier;
use App\ReadModel\Category\CategoryView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Domain\Cookie\ValueObject\CookieId;
use App\ReadModel\Cookie\GetCookieByIdQuery;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Category\AllCategoriesQuery;
use App\ReadModel\Cookie\CookiesDataGridQuery;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\Modal\Confirm\ConfirmModalControl;
use App\Domain\Cookie\Command\DeleteCookieCommand;
use App\ReadModel\Project\ProjectSelectOptionView;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\ReadModel\CookieProvider\FindCookieProviderSelectOptionsQuery;
use App\Domain\CookieProvider\Exception\CookieProviderNotFoundException;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;

final class CookieListControl extends Control
{
	private ValidLocalesProvider $validLocalesProvider;

	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private DataGridFactoryInterface $dataGridFactory;

	private ConfirmModalControlFactoryInterface $confirmModalControlFactory;

	private CookieFormModalControlFactoryInterface $cookieFormModalControlFactory;

	private ?CookieProviderId $cookieProviderId;

	private ?ProjectId $projectId = NULL;

	private bool $projectServiceOnly = FALSE;

	private bool $includeProjectsData = FALSE;

	private array $actions = [
		'update' => TRUE,
		'delete' => FALSE,
	];

	private array $acl = [
		'resource' => NULL,
		'update' => NULL,
		'delete' => NULL,
	];

	/**
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider                                        $validLocalesProvider
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface                            $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                              $queryBus
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface                                               $dataGridFactory
	 * @param \App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface                               $confirmModalControlFactory
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface $cookieFormModalControlFactory
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId|NULL                                $cookieProviderId
	 */
	public function __construct(
		ValidLocalesProvider $validLocalesProvider,
		CommandBusInterface $commandBus,
		QueryBusInterface $queryBus,
		DataGridFactoryInterface $dataGridFactory,
		ConfirmModalControlFactoryInterface $confirmModalControlFactory,
		CookieFormModalControlFactoryInterface $cookieFormModalControlFactory,
		?CookieProviderId $cookieProviderId = NULL
	) {
		$this->validLocalesProvider = $validLocalesProvider;
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->dataGridFactory = $dataGridFactory;
		$this->confirmModalControlFactory = $confirmModalControlFactory;
		$this->cookieFormModalControlFactory = $cookieFormModalControlFactory;
		$this->cookieProviderId = $cookieProviderId;
	}

	public function configureActions(bool $update, bool $delete): self
	{
		$this->actions = [
			'update' => $update,
			'delete' => $delete,
		];

		return $this;
	}

	/**
	 * @param string      $resource
	 * @param string|NULL $updatePrivilege
	 * @param string|NULL $deletePrivilege
	 *
	 * @return $this
	 */
	public function configureAclChecks(string $resource, ?string $updatePrivilege, ?string $deletePrivilege): self
	{
		$this->acl['resource'] = $resource;
		$this->acl['update'] = $updatePrivilege;
		$this->acl['delete'] = $deletePrivilege;

		return $this;
	}

	/**
	 * @param bool $includeProjectsData
	 *
	 * @return $this
	 */
	public function includeProjectsData(bool $includeProjectsData): self
	{
		$this->includeProjectsData = $includeProjectsData;

		return $this;
	}

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param bool                                      $servicesOnly
	 *
	 * @return $this
	 */
	public function projectOnly(ProjectId $projectId, bool $servicesOnly = FALSE): self
	{
		$this->projectId = $projectId;
		$this->projectServiceOnly = $servicesOnly;

		return $this;
	}

	/**
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$locale = $this->validLocalesProvider->getValidDefaultLocale();
		$query = CookiesDataGridQuery::create(NULL !== $locale ? $locale->code() : NULL)
			->withProjectsData($this->includeProjectsData);

		if (NULL !== $this->cookieProviderId) {
			$query = $query->withCookieProviderId($this->cookieProviderId->toString());
		}

		if (NULL !== $this->projectId) {
			$query = $query->withProjectId($this->projectId->toString(), $this->projectServiceOnly);
		}

		$grid = $this->dataGridFactory->create($query);

		$grid->setTranslator($this->getPrefixedTranslator());
		$grid->setSessionNamePostfix(NULL !== $this->cookieProviderId ? $this->cookieProviderId->toString() : '__all__');
		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');
		$grid->setTemplateVariables([
			'_locale' => $locale,
			'_acl' => $this->acl,
		]);

		$grid->setDefaultSort([
			'created_at' => 'DESC',
		]);

		$grid->addColumnText('name', 'name', 'cookieName.value')
			->setSortable('cookieName')
			->setFilterText('cookieName');

		$grid->addColumnText('active', 'active')
			->setAlign('center')
			->setFilterSelect(FilterHelper::bool($grid->getTranslator()));

		$grid->addColumnText('category_name', 'category_name')
			->setSortable('categoryName')
			->setFilterMultiSelect($this->getCategories(), 'categoryId');

		$grid->addColumnText('processing_time', 'processing_time');

		if (NULL === $this->cookieProviderId) {
			$providers = $this->getCookieProviders();
			$providerOptions = [];

			foreach ($providers as $cookieProviderSelectOptionView) {
				$providerOptions += $cookieProviderSelectOptionView->toOption();
			}

			$grid->addTemplateVariable('providers', array_combine(array_keys($providerOptions), $providers));

			$grid->addColumnText('provider_name', 'provider_name')
				->setSortable('providerName')
				->setFilterMultiSelect($providerOptions, 'providerId');

			$grid->addColumnText('provider_type', 'provider_type')
				->setFilterSelect(FilterHelper::select(ProviderType::values(), FALSE, $grid->getTranslator(), '//layout.cookie_provider_type.'), 'providerType');
		}

		if ($this->includeProjectsData) {
			$grid->addColumnText('projects', 'projects')
				->setFilterMultiSelect($this->getProjects(), 'projects');
		}

		$grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('createdAt')
			->setFilterDate('createdAt');

		if ($this->actions['update']) {
			$grid->addAction('edit', '')
				->setTemplate(__DIR__ . '/templates/action.edit.latte', [
					'_acl' => $this->acl,
				]);
		}

		if ($this->actions['delete']) {
			$grid->addAction('delete', '')
				->setTemplate(__DIR__ . '/templates/action.delete.latte', [
					'_acl' => $this->acl,
				]);
		}

		return $grid;
	}

	/**
	 * @return \Nette\Application\UI\Multiplier
	 */
	protected function createComponentDeleteConfirm(): Multiplier
	{
		if (!$this->actions['delete'] || (isset($this->acl['resource'], $this->acl['delete']) && !$this->getUser()->isAllowed($this->acl['resource'], $this->acl['delete']))) {
			throw new InvalidStateException('The user is not allowed to delete cookies.');
		}

		return new Multiplier(function (string $id): ConfirmModalControl {
			$cookieId = CookieId::fromString($id);
			$cookieView = $this->queryBus->dispatch(GetCookieByIdQuery::create($cookieId->toString()));

			if (!$cookieView instanceof CookieView || (NULL !== $this->cookieProviderId && !$cookieView->cookieProviderId->equals($this->cookieProviderId))) {
				throw new InvalidStateException('Cookie provider not found.');
			}

			$name = $cookieView->name->value();

			return $this->confirmModalControlFactory->create(
				'',
				$this->getPrefixedTranslator()->translate('delete_confirm.question', ['name' => $name]),
				function () use ($cookieView) {
					try {
						$this->commandBus->dispatch(DeleteCookieCommand::create($cookieView->id->toString()));
					} catch (CookieProviderNotFoundException $e) {
					}

					$this->getComponent('grid')->reload();
					$this->closeModal();
				}
			);
		});
	}

	/**
	 * @return \Nette\Application\UI\Multiplier
	 */
	protected function createComponentEditModal(): Multiplier
	{
		if (!$this->actions['update'] || (isset($this->acl['resource'], $this->acl['update']) && !$this->getUser()->isAllowed($this->acl['resource'], $this->acl['update']))) {
			throw new InvalidStateException('The user is not allowed to update cookies.');
		}

		return new Multiplier(function (string $id): CookieFormModalControl {
			$cookieId = CookieId::fromString($id);
			$cookieView = $this->queryBus->dispatch(GetCookieByIdQuery::create($cookieId->toString()));

			if (!$cookieView instanceof CookieView || (NULL !== $this->cookieProviderId && !$cookieView->cookieProviderId->equals($this->cookieProviderId))) {
				throw new InvalidStateException('Cookie provider not found.');
			}

			$control = $this->cookieFormModalControlFactory->create($this->validLocalesProvider, $cookieView);
			$inner = $control->getInnerControl();

			if (NULL !== $this->cookieProviderId) {
				$inner->setCookieProviderId($this->cookieProviderId);
			}

			$inner->setFormFactoryOptions([
				FormFactoryInterface::OPTION_AJAX => TRUE,
			]);

			$inner->addEventListener(CookieUpdatedEvent::class, function (CookieUpdatedEvent $event) {
				$this->subscribeFlashMessage(FlashMessage::success('cookie_updated'));
				$this['grid']->redrawItem($event->cookieId()->toString());
				$this->closeModal();
			});

			$inner->addEventListener(CookieFormProcessingFailedEvent::class, function () {
				$this->subscribeFlashMessage(FlashMessage::error('cookie_update_failed'));
			});

			return $control;
		});
	}

	/**
	 * @return array
	 */
	private function getCategories(): array
	{
		$locale = $this->validLocalesProvider->getValidDefaultLocale();
		$categories = [];

		foreach ($this->queryBus->dispatch(AllCategoriesQuery::create()) as $categoryView) {
			assert($categoryView instanceof CategoryView);

			$categories[$categoryView->id->toString()] = NULL !== $locale && isset($categoryView->names[$locale->code()]) ? $categoryView->names[$locale->code()]->value() : $categoryView->code->value();
		}

		return $categories;
	}

	/**
	 * @return \App\ReadModel\CookieProvider\CookieProviderSelectOptionView[]
	 */
	private function getCookieProviders(): array
	{
		$query = FindCookieProviderSelectOptionsQuery::all()
			->withPrivate(TRUE);

		return $this->queryBus->dispatch($query);
	}

	/**
	 * @return array
	 */
	private function getProjects(): array
	{
		$options = [];

		foreach ($this->queryBus->dispatch(FindProjectSelectOptionsQuery::all()) as $projectSelectOptionView) {
			assert($projectSelectOptionView instanceof ProjectSelectOptionView);

			$options += $projectSelectOptionView->toOption();
		}

		return $options;
	}
}
