<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProjectCookieSuggestionList;

use App\ReadModel\Project\ProjectCookieSuggestionsDataGridQuery;
use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use Ublaboo\DataGrid\Exception\DataGridException;

final class ProjectCookieSuggestionListControl extends Control
{
    private DataGridFactoryInterface $dataGridFactory;

    public function __construct(
        DataGridFactoryInterface $dataGridFactory,
    ) {
        $this->dataGridFactory = $dataGridFactory;
    }

    /**
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        $grid = $this->dataGridFactory->create(ProjectCookieSuggestionsDataGridQuery::create());

        $grid->setTranslator($this->getPrefixedTranslator());
        $grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');

        $grid->setDefaultSort([
            'latest_found_at' => 'DESC',
        ]);

        $grid->addColumnText('name', 'name')
            ->setSortable('name')
            ->setFilterText('name');

        $grid->addColumnText('code', 'code')
            ->setSortable('code')
            ->setFilterText('code');

        $grid->addColumnText('statistics', 'statistics')
            ->setAlign('center')
            ->setSortable('statisticsTotal');

        $grid->addColumnDateTimeTz('latest_found_at', 'latest_found_at', 'statistics.latestFoundAt')
            ->setFormat('j.n.Y H:i:s')
            ->setReplacement([
                '' => '-',
            ])
            ->setSortable('latestFoundAt');

        $grid->addAction('detail', '')
            ->setTemplate(__DIR__ . '/templates/action.detail.latte');

        return $grid;
    }
}
