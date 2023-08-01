<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid;

use Ublaboo\DataGrid\Exception\DataGridHasToBeAttachedToPresenterComponentException;
use Ublaboo\DataGrid\Traits\TLink;

final class LinkFactory
{
    use TLink;

    private DataGrid $dataGrid;

    public function __construct(DataGrid $dataGrid)
    {
        $this->dataGrid = $dataGrid;
    }

    /**
     * @throws DataGridHasToBeAttachedToPresenterComponentException
     */
    public function link(string $href, array $params): string
    {
        return $this->createLink($this->dataGrid, $href, $params);
    }

    /**
     * @throws DataGridHasToBeAttachedToPresenterComponentException
     */
    public function __invoke(string $href, array $params): string
    {
        return $this->link($href, $params);
    }
}
