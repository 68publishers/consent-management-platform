<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\PasswordRequestList;

use App\ReadModel\PasswordRequest\PasswordRequestsDataGridQuery;
use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\ValueObject\Status;
use Ublaboo\DataGrid\Exception\DataGridException;

final class PasswordRequestListControl extends Control
{
    public function __construct(
        private readonly DataGridFactoryInterface $dataGridFactory,
    ) {}

    /**
     * @throws DataGridException
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
            ->setAlign('center')
            ->setFilterMultiSelect(FilterHelper::items(Status::values(), false, $this->getPrefixedTranslator(), 'status_value.'));

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
