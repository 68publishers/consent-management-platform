<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid;

use Ublaboo\DataGrid\Traits\TLink;

final class LinkFactory
{
	use TLink;

	private DataGrid $dataGrid;

	/**
	 * @param \App\Web\Ui\DataGrid\DataGrid $dataGrid
	 */
	public function __construct(DataGrid $dataGrid)
	{
		$this->dataGrid = $dataGrid;
	}

	/**
	 * @param string $href
	 * @param array  $params
	 *
	 * @return string
	 * @throws \Ublaboo\DataGrid\Exception\DataGridHasToBeAttachedToPresenterComponentException
	 */
	public function link(string $href, array $params): string
	{
		return $this->createLink($this->dataGrid, $href, $params);
	}

	/**
	 * @param string $href
	 * @param array  $params
	 *
	 * @return string
	 * @throws \Ublaboo\DataGrid\Exception\DataGridHasToBeAttachedToPresenterComponentException
	 */
	public function __invoke(string $href, array $params): string
	{
		return $this->link($href, $params);
	}
}
