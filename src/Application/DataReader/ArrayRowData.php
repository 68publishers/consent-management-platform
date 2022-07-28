<?php

declare(strict_types=1);

namespace App\Application\DataReader;

final class ArrayRowData implements RowDataInterface
{
	private array $array;

	private function __construct()
	{
	}

	/**
	 * @param array $array
	 *
	 * @return static
	 */
	public static function create(array $array): self
	{
		$data = new self();
		$data->array = $array;

		return $data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function has($column): bool
	{
		return array_key_exists($column, $this->array);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get($column, $default = NULL)
	{
		return $this->array[$column] ?? $default;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return $this->array;
	}
}
