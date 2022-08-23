<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

interface RowDataInterface
{
	/**
	 * @param string|int $column
	 *
	 * @return bool
	 */
	public function has($column): bool;

	/**
	 * @param string|int $column
	 * @param mixed      $default
	 *
	 * @return mixed
	 */
	public function get($column, $default = NULL);

	/**
	 * @return array
	 */
	public function toArray(): array;
}
