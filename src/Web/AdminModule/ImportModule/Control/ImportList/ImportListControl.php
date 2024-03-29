<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportList;

use App\Application\Import\Helper\KnownDescriptors;
use App\Domain\Import\ValueObject\Status;
use App\ReadModel\Import\GetImportByIdQuery;
use App\ReadModel\Import\ImportDataGridQuery;
use App\ReadModel\Import\ImportListView;
use App\ReadModel\Import\ImportView;
use App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailModalControl;
use App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailModalControlFactoryInterface;
use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use Nette\Application\UI\Multiplier;
use Nette\InvalidStateException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Ublaboo\DataGrid\Exception\DataGridException;

final class ImportListControl extends Control
{
    public function __construct(
        private readonly DataGridFactoryInterface $dataGridFactory,
        private readonly ImportDetailModalControlFactoryInterface $importDetailModalControlFactory,
        private readonly QueryBusInterface $queryBus,
    ) {}

    /**
     * @throws DataGridException
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
            ->setFilterMultiSelect(FilterHelper::items(KnownDescriptors::ALL, false, $grid->getTranslator(), '//imports.name.'), 'name');

        $grid->addColumnText('status', 'status', 'status.value')
            ->setAlign('center')
            ->setFilterMultiSelect(FilterHelper::items(Status::values(), false, $grid->getTranslator(), 'status_value.'), 'status');

        $grid->addColumnText('author', 'author')
            ->setSortable('authorName')
            ->setFilterText('authorName');

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
                'createLink' => fn (ImportListView $view): string => $this->link('openModal!', ['modal' => 'detail-' . $view->id->id()->getHex()->toString()]),
            ]);

        return $grid;
    }

    protected function createComponentDetail(): Multiplier
    {
        return new Multiplier(function (string $importId): ImportDetailModalControl {
            $importView = $this->queryBus->dispatch(GetImportByIdQuery::create($importId));

            if (!$importView instanceof ImportView) {
                throw new InvalidStateException(sprintf(
                    'Import with ID %s not found.',
                    $importId,
                ));
            }

            return $this->importDetailModalControlFactory->create($importView);
        });
    }
}
