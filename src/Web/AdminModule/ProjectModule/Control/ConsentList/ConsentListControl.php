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
use App\ReadModel\DataGridQueryInterface;
use App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailModalControlFactoryInterface;
use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\DataGrid\DataSource\ReadModelDataSource;
use Nette\Application\UI\Multiplier;
use Nette\InvalidStateException;
use Ramsey\Uuid\Uuid;
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
        $grid->addTemplateVariable('paginatorMaxItemsCount', ConsentsDataGridQuery::COUNT_LIMIT);

        $grid->setDefaultSort([
            'last_update_at' => 'DESC',
        ]);

        $grid->addColumnText('user_identifier', 'user_identifier', 'userIdentifier')
            ->setSortable('userIdentifier')
            ->setFilterText('userIdentifier');

        $grid->addColumnText('settings_short_identifier', 'settings_short_identifier', 'settingsShortIdentifier');

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
                'createLink' => fn (ConsentListView $view): string => $this->link('openModal!', ['modal' => 'history-' . Uuid::fromString($view->id)->getHex()->toString()]),
            ]);

        $dataModel = $grid->getDataModel();

        if (null !== $dataModel) {
            $dataModel->onAfterPaginated[] = function (ReadModelDataSource $dataSource) use ($grid): void {
                $paginator = $grid->getPaginator()?->getPaginator();

                if (null === $paginator || $paginator->getItemCount() < ConsentsDataGridQuery::COUNT_LIMIT || !$paginator->isLast()) {
                    return;
                }

                $additionalPages = $grid->page - $paginator->page + 1;
                $itemCount = $paginator->getItemCount() + ($additionalPages * $paginator->getItemsPerPage());
                $paginator->setItemCount($itemCount);

                $dataSource->limit(
                    $paginator->getOffset(),
                    $paginator->getItemsPerPage(),
                );

                $dataSource->onData[] = function (array $data, DataGridQueryInterface $query) use ($paginator): array {
                    if ($query::MODE_DATA !== $query->mode() || count($data) >= $paginator->getItemsPerPage()) {
                        return $data;
                    }

                    $paginator->setItemCount($paginator->getItemCount() - $paginator->getItemsPerPage() - ($paginator->getItemsPerPage() - count($data)));

                    return $data;
                };
            };
        }

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
