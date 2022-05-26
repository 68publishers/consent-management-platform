<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryList;

use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\ReadModel\Category\CategoriesDataGridQuery;

final class CategoryListControl extends Control
{
	private DataGridFactoryInterface $dataGridFactory;

	/**
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface $dataGridFactory
	 */
	public function __construct(DataGridFactoryInterface $dataGridFactory)
	{
		$this->dataGridFactory = $dataGridFactory;
	}

	/**
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create(CategoriesDataGridQuery::create());

		$grid->setTranslator($this->getPrefixedTranslator());
		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');

		$grid->setDefaultSort([
			'created_at' => 'DESC',
		]);

		$grid->addColumnText('name', 'name');

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

		return $grid;
	}
}
