<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentList;

use App\Web\Ui\Control;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use Nette\Application\UI\Multiplier;
use App\ReadModel\Consent\ConsentView;
use App\ReadModel\Consent\ConsentListView;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Consent\ConsentsDataGridQuery;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\Consent\GetConsentByIdAndProjectIdQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\ReadModel\ConsentSettings\GetConsentSettingsByIdAndProjectIdQuery;
use App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControlFactoryInterface;

final class ConsentListControl extends Control
{
	private ProjectId $projectId;

	private DataGridFactoryInterface $dataGridFactory;

	private ConsentHistoryModalControlFactoryInterface $consentHistoryModalControlFactory;

	private ConsentSettingsDetailModalControlFactoryInterface $consentSettingsDetailModalControlFactory;

	private QueryBusInterface $queryBus;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId                                                                          $projectId
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface                                                                      $dataGridFactory
	 * @param \App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControlFactoryInterface               $consentHistoryModalControlFactory
	 * @param \App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControlFactoryInterface $consentSettingsDetailModalControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                                                     $queryBus
	 */
	public function __construct(ProjectId $projectId, DataGridFactoryInterface $dataGridFactory, ConsentHistoryModalControlFactoryInterface $consentHistoryModalControlFactory, ConsentSettingsDetailModalControlFactoryInterface $consentSettingsDetailModalControlFactory, QueryBusInterface $queryBus)
	{
		$this->projectId = $projectId;
		$this->dataGridFactory = $dataGridFactory;
		$this->consentHistoryModalControlFactory = $consentHistoryModalControlFactory;
		$this->consentSettingsDetailModalControlFactory = $consentSettingsDetailModalControlFactory;
		$this->queryBus = $queryBus;
	}

	/**
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create(ConsentsDataGridQuery::create($this->projectId->toString()));

		$grid->setSessionNamePostfix('p' . $this->projectId->toString());
		$grid->setTranslator($this->getPrefixedTranslator());
		$grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');

		$grid->setDefaultSort([
			'last_update_at' => 'DESC',
		]);

		$grid->addColumnText('user_identifier', 'user_identifier', 'userIdentifier.value')
			->setSortable('userIdentifier')
			->setFilterText('userIdentifier');

		$grid->addColumnText('settings_short_identifier', 'settings_short_identifier');

		$grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('createdAt')
			->setFilterDate('createdAt');

		$grid->addColumnDateTimeTz('last_update_at', 'last_update_at', 'lastUpdateAt')
			->setFormat('j.n.Y H:i:s')
			->setSortable('lastUpdateAt')
			->setFilterDate('lastUpdateAt');

		$grid->addAction('edit', '')
			->setTemplate(__DIR__ . '/templates/action.detail.latte', [
				'createLink' => fn (ConsentListView $view): string => $this->link('openModal!', ['modal' => 'history-' . $view->id->id()->getHex()->toString()]),
			]);

		return $grid;
	}

	/**
	 * @return \Nette\Application\UI\Multiplier
	 */
	protected function createComponentHistory(): Multiplier
	{
		return new Multiplier(function (string $consentId): ConsentHistoryModalControl {
			$consentView = $this->queryBus->dispatch(GetConsentByIdAndProjectIdQuery::create($consentId, $this->projectId->toString()));

			if (!$consentView instanceof ConsentView) {
				throw new InvalidStateException(sprintf(
					'Consent for ID %s not found.',
					$consentId
				));
			}

			return $this->consentHistoryModalControlFactory->create($consentView);
		});
	}

	/**
	 * @return \Nette\Application\UI\Multiplier
	 */
	protected function createComponentConsentSettingsDetail(): Multiplier
	{
		return new Multiplier(function (string $consentSettingsId): ConsentSettingsDetailModalControl {
			$consentSettingsView = $this->queryBus->dispatch(GetConsentSettingsByIdAndProjectIdQuery::create($consentSettingsId, $this->projectId->toString()));

			if (!$consentSettingsView instanceof ConsentSettingsView) {
				throw new InvalidStateException(sprintf(
					'Consent settings for checksum %s not found.',
					$consentSettingsId
				));
			}

			return $this->consentSettingsDetailModalControlFactory->create($consentSettingsView);
		});
	}
}
