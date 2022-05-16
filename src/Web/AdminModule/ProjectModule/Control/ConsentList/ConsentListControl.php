<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentList;

use App\Web\Ui\Control;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use Nette\Application\UI\Multiplier;
use App\ReadModel\Consent\ConsentView;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Consent\ConsentsDataGridQuery;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\ReadModel\Consent\GetConsentByIdAndProjectIdQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControlFactoryInterface;

final class ConsentListControl extends Control
{
	private ProjectId $projectId;

	private DataGridFactoryInterface $dataGridFactory;

	private ConsentHistoryModalControlFactoryInterface $consentHistoryModalControlFactory;

	private QueryBusInterface $queryBus;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId                                                            $projectId
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface                                                        $dataGridFactory
	 * @param \App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControlFactoryInterface $consentHistoryModalControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                                       $queryBus
	 */
	public function __construct(ProjectId $projectId, DataGridFactoryInterface $dataGridFactory, ConsentHistoryModalControlFactoryInterface $consentHistoryModalControlFactory, QueryBusInterface $queryBus)
	{
		$this->projectId = $projectId;
		$this->dataGridFactory = $dataGridFactory;
		$this->consentHistoryModalControlFactory = $consentHistoryModalControlFactory;
		$this->queryBus = $queryBus;
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

		$grid->addAction('edit', '')
			->setTemplate(__DIR__ . '/templates/action.detail.latte', [
				'createLink' => fn (ConsentView $view): string => $this->link('openModal!', ['modal' => 'history-' . $view->id->id()->getHex()->toString()]),
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
}
