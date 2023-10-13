<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentList;

use App\Application\GlobalSettings\EnabledEnvironmentsResolver;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\GlobalSettings\ValueObject\Environment;
use App\Domain\Project\ValueObject\Environments;
use App\Domain\Project\ValueObject\ProjectId;
use App\Infrastructure\Consent\Doctrine\ReadModel\ConsentsDataGridQueryHandler;
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
        private readonly Environments $projectEnvironments,
        private readonly DataGridFactoryInterface $dataGridFactory,
        private readonly ConsentHistoryModalControlFactoryInterface $consentHistoryModalControlFactory,
        private readonly ConsentSettingsDetailModalControlFactoryInterface $consentSettingsDetailModalControlFactory,
        private readonly QueryBusInterface $queryBus,
        private readonly GlobalSettingsInterface $globalSettings,
        private readonly ?int $countLimit = null,
    ) {}

    /**
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        $query = ConsentsDataGridQuery::create(
            projectId: $this->projectId->toString(),
            countLimit: $this->countLimit,
        );
        $environments = EnabledEnvironmentsResolver::resolveProjectEnvironments(
            globalSettingsEnvironments: $this->globalSettings->environments(),
            projectEnvironments: $this->projectEnvironments,
        );

        $grid = $this->dataGridFactory->create($query);

        $grid->setSessionNamePostfix('p' . $this->projectId->toString());
        $grid->setTranslator($this->getPrefixedTranslator());
        $grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');
        $grid->addTemplateVariable('paginatorMaxItemsCount', $query->getCountLimit());
        $grid->addTemplateVariable('environments', $environments);

        $translator = $grid->getTranslator();

        $grid->setDefaultSort([
            'last_update_at' => 'DESC',
        ]);

        $grid->addColumnText('user_identifier', 'user_identifier', 'userIdentifier')
            ->setSortable('userIdentifier')
            ->setFilterText('userIdentifier');

        $grid->addColumnText('settings_short_identifier', 'settings_short_identifier', 'settingsShortIdentifier')
            ->setAlign('center');

        if (0 < count($environments)) {
            $grid->addColumnText('environment', 'environment', 'environment')
                ->setAlign('center')
                ->setFilterSelect(
                    options: [
                        '' => $translator->translate('//layout.all_environments'),
                        ConsentsDataGridQueryHandler::FILTER_ENVIRONMENT_DEFAULT_ENV_VALUE => $translator->translate('//layout.default_environment'),
                    ]
                    + array_map(
                        static fn (Environment $environment): string => $environment->name,
                        $environments,
                    ),
                    column: 'environment',
                );
        }

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
            $dataModel->onAfterPaginated[] = function (ReadModelDataSource $dataSource) use ($grid, $query): void {
                $paginator = $grid->getPaginator()?->getPaginator();

                if (null === $paginator || $paginator->getItemCount() < $query->getCountLimit() || !$paginator->isLast()) {
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
