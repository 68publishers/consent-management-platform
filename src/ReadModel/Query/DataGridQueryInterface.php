<?php

declare(strict_types=1);

namespace App\ReadModel\Query;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\PaginatedQueryInterface;

interface DataGridQueryInterface extends PaginatedQueryInterface
{
	public const MODE_DATA = 'data';
	public const MODE_ONE = 'one';
	public const MODE_COUNT = 'count';

	/**
	 * @return array
	 */
	public function filters(): array;

	/**
	 * @return array
	 */
	public function sorting(): array;

	/**
	 * @return string
	 */
	public function mode(): string;

	/**
	 * @param string $name
	 * @param $value
	 *
	 * @return $this
	 */
	public function withFilter(string $name, $value): self;

	/**
	 * @param string $name
	 * @param string $direction
	 *
	 * @return $this
	 */
	public function withSorting(string $name, string $direction): self;

	/**
	 * @return $this
	 */
	public function withDataMode(): self;

	/**
	 * @return $this
	 */
	public function withOneMode(): self;

	/**
	 * @return $this
	 */
	public function withCountMode(): self;
}
