<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentList;

use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Consent\ConsentListView;
use App\ReadModel\Consent\ConsentsDataGridQuery;
use App\ReadModel\Consent\ConsentView;
use App\ReadModel\Consent\GetConsentByIdAndProjectIdQuery;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\GetConsentSettingsByIdAndProjectIdQuery;
use App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControlFactoryInterface;
use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use Nette\Application\UI\Multiplier;
use Nette\InvalidStateException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Ublaboo\DataGrid\Exception\DataGridException;

final class ConsentListControl extends Control
{
    public function __construct(
        private readonly ProjectId $projectId,
        private readonly DataGridFactoryInterface $dataGridFactory,
        private readonly ConsentHistoryModalControlFactoryInterface $consentHistoryModalControlFactory,
        private readonly ConsentSettingsDetailModalControlFactoryInterface $consentSettingsDetailModalControlFactory,
        private readonly QueryBusInterface $queryBus,
    ) {}

    /**
     * @throws DataGridException
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

    protected function createComponentHistory(): Multiplier
    {
        return new Multiplier(function (string $consentId): ConsentHistoryModalControl {
            $consentView = $this->queryBus->dispatch(GetConsentByIdAndProjectIdQuery::create($consentId, $this->projectId->toString()));

            if (!$consentView instanceof ConsentView) {
                throw new InvalidStateException(sprintf(
                    'Consent for ID %s not found.',
                    $consentId,
                ));
            }

            return $this->consentHistoryModalControlFactory->create($consentView);
        });
    }

    protected function createComponentConsentSettingsDetail(): Multiplier
    {
        return new Multiplier(function (string $consentSettingsId): ConsentSettingsDetailModalControl {
            $consentSettingsView = $this->queryBus->dispatch(GetConsentSettingsByIdAndProjectIdQuery::create($consentSettingsId, $this->projectId->toString()));

            if (!$consentSettingsView instanceof ConsentSettingsView) {
                throw new InvalidStateException(sprintf(
                    'Consent settings for checksum %s not found.',
                    $consentSettingsId,
                ));
            }

            return $this->consentSettingsDetailModalControlFactory->create($consentSettingsView);
        });
    }
}
