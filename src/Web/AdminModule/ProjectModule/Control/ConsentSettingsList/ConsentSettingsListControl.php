<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList;

use App\Web\Ui\Control;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use Nette\Application\UI\Multiplier;
use App\Domain\Project\ValueObject\ProjectId;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\ConsentSettingsDataGridQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\ReadModel\ConsentSettings\GetConsentSettingsByIdAndProjectIdQuery;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControlFactoryInterface;

final class ConsentSettingsListControl extends Control
{
	private ProjectId $projectId;

	private DataGridFactoryInterface $dataGridFactory;

	private QueryBusInterface $queryBus;

	private ConsentSettingsDetailModalControlFactoryInterface $consentSettingsDetailModalControlFactory;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId                                                                          $projectId
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface                                                                      $dataGridFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                                                     $queryBus
	 * @param \App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControlFactoryInterface $consentSettingsDetailModalControlFactory
	 */
	public function __construct(ProjectId $projectId, DataGridFactoryInterface $dataGridFactory, QueryBusInterface $queryBus, ConsentSettingsDetailModalControlFactoryInterface $consentSettingsDetailModalControlFactory)
	{
		$this->projectId = $projectId;
		$this->dataGridFactory = $dataGridFactory;
		$this->queryBus = $queryBus;
		$this->consentSettingsDetailModalControlFactory = $consentSettingsDetailModalControlFactory;
	}

	/**
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create(ConsentSettingsDataGridQuery::create($this->projectId->toString()));

		$grid->setTranslator($this->getPrefixedTranslator());
		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');

		$grid->setDefaultSort([
			'last_update_at' => 'DESC',
		]);

		$grid->addColumnText('short_identifier', 'short_identifier', 'shortIdentifier.value')
			->setSortable('shortIdentifier')
			->setFilterText('shortIdentifier');

		$grid->addColumnText('checksum', 'checksum')
			->setSortable('checksum')
			->setFilterText('checksum');

		$grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('createdAt')
			->setFilterDate('createdAt');

		$grid->addColumnDateTimeTz('last_update_at', 'last_update_at', 'lastUpdateAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('lastUpdateAt')
			->setFilterDate('lastUpdateAt');

		$grid->addAction('detail', '')
			->setTemplate(__DIR__ . '/templates/action.detail.latte', [
				'createLink' => fn (ConsentSettingsView $view): string => $this->link('openModal!', ['modal' => 'detail-' . $view->id->id()->getHex()->toString()]),
			]);

		return $grid;
	}

	/**
	 * @return \Nette\Application\UI\Multiplier
	 */
	protected function createComponentDetail(): Multiplier
	{
		return new Multiplier(function (string $consentSettingsId): ConsentSettingsDetailModalControl {
			$consentSettingsView = $this->queryBus->dispatch(GetConsentSettingsByIdAndProjectIdQuery::create($consentSettingsId, $this->projectId->toString()));

			if (!$consentSettingsView instanceof ConsentSettingsView) {
				throw new InvalidStateException(sprintf(
					'Consent settings for ID %s not found.',
					$consentSettingsId
				));
			}

			return $this->consentSettingsDetailModalControlFactory->create($consentSettingsView);
		});
	}
}
