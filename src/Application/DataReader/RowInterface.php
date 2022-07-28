<?php

declare(strict_types=1);

namespace App\Application\DataReader;

interface RowInterface
{
	/**
	 * @param string                                       $index
	 * @param \App\Application\DataReader\RowDataInterface $data
	 *
	 * @return static
	 */
	public static function create(string $index, RowDataInterface $data): self;

	/**
	 * @param \App\Application\DataReader\RowDataInterface $data
	 *
	 * @return $this
	 */
	public function withData(RowDataInterface $data): self;

	/**
	 * @return string
	 */
	public function index(): string;

	/**
	 * @return \App\Application\DataReader\RowDataInterface
	 */
	public function data(): RowDataInterface;
}
