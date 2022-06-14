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
use App\Application\GlobalSettings\Locale;
use App\Domain\Cookie\ValueObject\CookieId;
use App\ReadModel\Cookie\GetCookieByIdQuery;
use App\ReadModel\Category\AllCategoriesQuery;
use App\ReadModel\Cookie\CookiesDataGridQuery;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\Modal\Confirm\ConfirmModalControl;
use App\Domain\Cookie\Command\DeleteCookieCommand;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Domain\CookieProvider\Exception\CookieProviderNotFoundException;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControl;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\Event\CookieFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface;

final class CookieListControl extends Control
{
	private CookieProviderId $cookieProviderId;

	private ?Locale $locale;

	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private DataGridFactoryInterface $dataGridFactory;

	private ConfirmModalControlFactoryInterface $confirmModalControlFactory;

	private CookieFormModalControlFactoryInterface $cookieFormModalControlFactory;

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId                                     $cookieProviderId
	 * @param \App\Application\GlobalSettings\Locale|NULL                                                 $locale
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface                            $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                              $queryBus
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface                                               $dataGridFactory
	 * @param \App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface                               $confirmModalControlFactory
	 * @param \App\Web\AdminModule\CookieModule\Control\CookieForm\CookieFormModalControlFactoryInterface $cookieFormModalControlFactory
	 */
	public function __construct(CookieProviderId $cookieProviderId, ?Locale $locale, CommandBusInterface $commandBus, QueryBusInterface $queryBus, DataGridFactoryInterface $dataGridFactory, ConfirmModalControlFactoryInterface $confirmModalControlFactory, CookieFormModalControlFactoryInterface $cookieFormModalControlFactory)
	{
		$this->cookieProviderId = $cookieProviderId;
		$this->locale = $locale;
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->dataGridFactory = $dataGridFactory;
		$this->confirmModalControlFactory = $confirmModalControlFactory;
		$this->cookieFormModalControlFactory = $cookieFormModalControlFactory;
	}

	/**
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create(CookiesDataGridQuery::create($this->cookieProviderId->toString(), NULL !== $this->locale ? $this->locale->code() : NULL));

		$grid->setTranslator($this->getPrefixedTranslator());
		$grid->setSessionNamePostfix($this->cookieProviderId->toString());
		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');
		$grid->setTemplateVariables([
			'_locale' => $this->locale,
		]);

		$grid->setDefaultSort([
			'created_at' => 'DESC',
		]);

		$grid->addColumnText('name', 'name', 'cookieName.value')
			->setSortable('cookieName')
			->setFilterText('cookieName');

		$grid->addColumnText('category_name', 'category_name')
			->setSortable('categoryName')
			->setFilterMultiSelect($this->getCategories(), 'categoryName');

		$grid->addColumnText('processing_time', 'processing_time');

		$grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('createdAt')
			->setFilterDate('createdAt');

		$grid->addAction('edit', '')
			->setTemplate(__DIR__ . '/templates/action.edit.latte');

		$grid->addAction('delete', '')
			->setTemplate(__DIR__ . '/templates/action.delete.latte');

		return $grid;
	}

	/**
	 * @return \Nette\Application\UI\Multiplier
	 */
	protected function createComponentDeleteConfirm(): Multiplier
	{
		return new Multiplier(function (string $id): ConfirmModalControl {
			$cookieId = CookieId::fromString($id);
			$cookieView = $this->queryBus->dispatch(GetCookieByIdQuery::create($cookieId->toString()));

			if (!$cookieView instanceof CookieView || !$cookieView->cookieProviderId->equals($this->cookieProviderId)) {
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

	protected function createComponentEditModal(): Multiplier
	{
		return new Multiplier(function (string $id): CookieFormModalControl {
			$cookieId = CookieId::fromString($id);
			$cookieView = $this->queryBus->dispatch(GetCookieByIdQuery::create($cookieId->toString()));

			if (!$cookieView instanceof CookieView || !$cookieView->cookieProviderId->equals($this->cookieProviderId)) {
				throw new InvalidStateException('Cookie provider not found.');
			}

			$control = $this->cookieFormModalControlFactory->create($this->cookieProviderId, $cookieView);
			$inner = $control->getInnerControl();

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
		$categories = [];

		foreach ($this->queryBus->dispatch(AllCategoriesQuery::create()) as $categoryView) {
			assert($categoryView instanceof CategoryView);

			$categories[$categoryView->id->toString()] = NULL !== $this->locale && isset($categoryView->names[$this->locale->code()]) ? $categoryView->names[$this->locale->code()]->value() : $categoryView->code->value();
		}

		return $categories;
	}
}
