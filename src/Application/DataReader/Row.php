<?php

declare(strict_types=1);

namespace App\Application\DataReader;

final class Row implements RowInterface
{
	private string $index;

	private RowDataInterface $data;

	private function __construct()
	{
	}

	/**
	 * {@inheritDoc}
	 */
	public static function create(string $index, RowDataInterface $data): self
	{
		$row = new self();
		$row->index = $index;
		$row->data = $data;

		return $row;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withData(RowDataInterface $data): RowInterface
	{
		$row = clone $this;
		$row->data = $data;

		return $row;
	}

	/**
	 * {@inheritDoc}
	 */
	public function index(): string
	{
		return $this->index;
	}

	/**
	 * {@inheritDoc}
	 */
	public function data(): RowDataInterface
	{
		return $this->data;
	}
}
