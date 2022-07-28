<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportList;

use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Domain\Import\ValueObject\Status;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\ReadModel\Import\ImportDataGridQuery;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Application\Import\Helper\KnownDescriptors;

final class ImportListControl extends Control
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
		$grid = $this->dataGridFactory->create(ImportDataGridQuery::create());

		$grid->setTranslator($this->getPrefixedTranslator());
		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');

		$grid->setDefaultSort([
			'created_at' => 'DESC',
		]);

		$grid->addColumnText('name', 'name', 'name.value')
			->setSortable('name')
			->setFilterMultiSelect(FilterHelper::items(KnownDescriptors::ALL, FALSE, $grid->getTranslator(), '//imports.name.'), 'name');

		$grid->addColumnText('status', 'status', 'status.value')
			->setFilterMultiSelect(FilterHelper::items(Status::values(), FALSE, $grid->getTranslator(), 'status_value.'), 'status');

		$grid->addColumnText('author', 'author', 'author.value')
			->setSortable('author')
			->setFilterText('author');

		$grid->addColumnNumber('imported', 'imported', 'imported.value')
			->setSortable('imported');

		$grid->addColumnNumber('failed', 'failed', 'failed.value')
			->setSortable('failed');

		$grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('createdAt')
			->setFilterDate('createdAt');

		$grid->addColumnDateTimeTz('ended_at', 'ended_at', 'endedAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('endedAt')
			->setFilterDate('endedAt');

		return $grid;
	}
}
