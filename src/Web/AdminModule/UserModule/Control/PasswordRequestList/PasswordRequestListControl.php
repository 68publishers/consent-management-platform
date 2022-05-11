<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\PasswordRequestList;

use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\ReadModel\PasswordRequest\PasswordRequestsDataGridQuery;

final class PasswordRequestListControl extends Control
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
		$grid = $this->dataGridFactory->create(PasswordRequestsDataGridQuery::create());

		$grid->setTranslator($this->getPrefixedTranslator());
		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');

		$grid->setDefaultSort([
			'requested_at' => 'DESC',
		]);

		$grid->addColumnText('id', 'id', 'id.toString')
			->setFilterText('id');

		$grid->addColumnText('email_address', 'email_address', 'emailAddress.value')
			->setSortable('emailAddress')
			->setFilterText('emailAddress');

		$grid->addColumnText('status', 'status')
			->setAlign('center');

		$grid->addColumnDateTimeTz('requested_at', 'requested_at', 'requestedAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('finishedAt')
			->setFilterDate('requestedAt');

		$grid->addColumnDateTimeTz('finished_at', 'finished_at', 'finishedAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('finishedAt')
			->setFilterDate('finishedAt');

		$grid->addColumnText('request_device_info', 'request_device_info');

		$grid->addColumnText('finished_device_info', 'finished_device_info');

		return $grid;
	}
}
