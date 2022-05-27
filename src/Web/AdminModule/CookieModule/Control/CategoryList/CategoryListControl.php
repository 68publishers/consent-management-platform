<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryList;

use App\Web\Ui\Control;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use Nette\Application\UI\Multiplier;
use App\ReadModel\Category\CategoryView;
use App\Application\GlobalSettings\Locale;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\Domain\Category\ValueObject\CategoryId;
use App\ReadModel\Category\GetCategoryByIdQuery;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\Modal\Confirm\ConfirmModalControl;
use App\ReadModel\Category\CategoriesDataGridQuery;
use App\Domain\Category\Command\DeleteCategoryCommand;
use App\Domain\Category\Exception\CategoryNotFoundException;
use App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class CategoryListControl extends Control
{
	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private DataGridFactoryInterface $dataGridFactory;

	private ConfirmModalControlFactoryInterface $confirmModalControlFactory;

	private ?Locale $locale;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface                    $dataGridFactory
	 * @param \App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface    $confirmModalControlFactory
	 * @param \App\Application\GlobalSettings\Locale|NULL                      $locale
	 */
	public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus, DataGridFactoryInterface $dataGridFactory, ConfirmModalControlFactoryInterface $confirmModalControlFactory, ?Locale $locale)
	{
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->dataGridFactory = $dataGridFactory;
		$this->confirmModalControlFactory = $confirmModalControlFactory;
		$this->locale = $locale;
	}

	/**
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create(CategoriesDataGridQuery::create(NULL !== $this->locale ? $this->locale->code() : NULL));

		$grid->setTranslator($this->getPrefixedTranslator());
		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');
		$grid->setTemplateVariables([
			'_locale' => $this->locale,
		]);

		$grid->setDefaultSort([
			'created_at' => 'DESC',
		]);

		$grid->addColumnText('name', 'name')
			->setFilterText('name');

		$grid->addColumnText('code', 'code', 'code.value')
			->setSortable('code')
			->setFilterText('code');

		$grid->addColumnText('active', 'active')
			->setAlign('center')
			->setFilterSelect(FilterHelper::bool($grid->getTranslator()));

		$grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('createdAt')
			->setFilterDate('createdAt');

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
			$categoryId = CategoryId::fromString($id);
			$categoryView = $this->queryBus->dispatch(GetCategoryByIdQuery::create($categoryId->toString()));

			if (!$categoryView instanceof CategoryView) {
				throw new InvalidStateException('Category not found.');
			}

			$name = NULL !== $this->locale && isset($categoryView->names[$this->locale->code()]) ? $categoryView->names[$this->locale->code()]->value() : '?';
			$code = $categoryView->code->value();

			return $this->confirmModalControlFactory->create(
				'',
				$this->getPrefixedTranslator()->translate('delete_confirm.question', ['name' => $name, 'code' => $code]),
				function () use ($categoryView) {
					try {
						$this->commandBus->dispatch(DeleteCategoryCommand::create($categoryView->id->toString()));
					} catch (CategoryNotFoundException $e) {
					}

					$this->getComponent('grid')->reload();
					$this->closeModal();
				}
			);
		});
	}
}
