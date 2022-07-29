<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportList;

use App\Web\Ui\Control;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use App\ReadModel\Import\ImportView;
use Nette\Application\UI\Multiplier;
use App\Domain\Import\ValueObject\Status;
use App\ReadModel\Import\GetImportByIdQuery;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\ReadModel\Import\ImportDataGridQuery;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Application\Import\Helper\KnownDescriptors;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailModalControl;
use App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailModalControlFactoryInterface;

final class ImportListControl extends Control
{
	private DataGridFactoryInterface $dataGridFactory;

	private ImportDetailModalControlFactoryInterface $importDetailModalControlFactory;

	private QueryBusInterface $queryBus;

	/**
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface                                                   $dataGridFactory
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailModalControlFactoryInterface $importDetailModalControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                                  $queryBus
	 */
	public function __construct(DataGridFactoryInterface $dataGridFactory, ImportDetailModalControlFactoryInterface $importDetailModalControlFactory, QueryBusInterface $queryBus)
	{
		$this->dataGridFactory = $dataGridFactory;
		$this->importDetailModalControlFactory = $importDetailModalControlFactory;
		$this->queryBus = $queryBus;
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
			->setAlign('center')
			->setFilterMultiSelect(FilterHelper::items(Status::values(), FALSE, $grid->getTranslator(), 'status_value.'), 'status');

		$grid->addColumnText('author', 'author', 'author.value')
			->setSortable('author')
			->setFilterText('author');

		$grid->addColumnText('summary', 'summary')
			->setAlign('center')
			->setSortable('imported');

		$grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('createdAt')
			->setFilterDate('createdAt');

		$grid->addColumnDateTimeTz('ended_at', 'ended_at', 'endedAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('endedAt')
			->setFilterDate('endedAt');

		$grid->addAction('edit', '')
			->setTemplate(__DIR__ . '/templates/action.detail.latte', [
				'createLink' => fn (ImportView $view): string => $this->link('openModal!', ['modal' => 'detail-' . $view->id->id()->getHex()->toString()]),
			]);

		return $grid;
	}

	/**
	 * @return \Nette\Application\UI\Multiplier
	 */
	protected function createComponentDetail(): Multiplier
	{
		return new Multiplier(function (string $importId): ImportDetailModalControl {
			$importView = $this->queryBus->dispatch(GetImportByIdQuery::create($importId));

			if (!$importView instanceof ImportView) {
				throw new InvalidStateException(sprintf(
					'Import with ID %s not found.',
					$importId
				));
			}

			return $this->importDetailModalControlFactory->create($importView);
		});
	}
}
