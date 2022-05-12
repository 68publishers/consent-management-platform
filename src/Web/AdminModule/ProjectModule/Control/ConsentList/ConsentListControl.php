<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentList;

use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Consent\ConsentsDataGridQuery;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;

final class ConsentListControl extends Control
{
	private ProjectId $projectId;

	private DataGridFactoryInterface $dataGridFactory;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId     $projectId
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface $dataGridFactory
	 */
	public function __construct(ProjectId $projectId, DataGridFactoryInterface $dataGridFactory)
	{
		$this->projectId = $projectId;
		$this->dataGridFactory = $dataGridFactory;
	}

	/**
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create(ConsentsDataGridQuery::create($this->projectId->toString()));

		$grid->setTranslator($this->getPrefixedTranslator());

		$grid->setDefaultSort([
			'created_at' => 'DESC',
		]);

		$grid->addColumnText('user_identifier', 'user_identifier', 'userIdentifier.value')
			->setSortable('userIdentifier')
			->setFilterText('userIdentifier');

		$grid->addColumnText('settings_checksum', 'settings_checksum', 'settingsChecksum.value')
			->setSortable('settingsChecksum')
			->setFilterText('settingsChecksum');

		$grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('createdAt')
			->setFilterDate('createdAt');

		$grid->addColumnDateTimeTz('last_update_at', 'last_update_at', 'lastUpdateAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('lastUpdateAt')
			->setFilterDate('lastUpdateAt');

		return $grid;
	}
}
