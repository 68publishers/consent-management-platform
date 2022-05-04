<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Filter;

interface ConvertibleTimezoneDateFilterInterface
{
	/**
	 * @return string
	 */
	public function getTimezoneFrom(): string;

	/**
	 * @return string
	 */
	public function getTimezoneTo(): string;
}
