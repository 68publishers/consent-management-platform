<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

interface RowInterface
{
	/**
	 * @param string                                          $index
	 * @param \App\Application\DataProcessor\RowDataInterface $data
	 *
	 * @return static
	 */
	public static function create(string $index, RowDataInterface $data): self;

	/**
	 * @param \App\Application\DataProcessor\RowDataInterface $data
	 *
	 * @return $this
	 */
	public function withData(RowDataInterface $data): self;

	/**
	 * @return string
	 */
	public function index(): string;

	/**
	 * @return \App\Application\DataProcessor\RowDataInterface
	 */
	public function data(): RowDataInterface;
}
