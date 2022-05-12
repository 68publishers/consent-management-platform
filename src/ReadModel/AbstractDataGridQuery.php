<?php

declare(strict_types=1);

namespace App\ReadModel;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractPaginatedQuery;

abstract class AbstractDataGridQuery extends AbstractPaginatedQuery implements DataGridQueryInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function filters(): array
	{
		return $this->getParam('filters') ?? [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function sorting(): array
	{
		return $this->getParam('sorting') ?? [];
	}

	/**
	 * @return string
	 */
	public function mode(): string
	{
		return $this->getParam('mode') ?? self::MODE_DATA;
	}

	/**
	 * {@inheritDoc}
	 */
	public function withFilter(string $name, $value): self
	{
		$filters = $this->getParam('filters') ?? [];
		$filters[$name] = $value;

		return $this->withParam('filters', $filters);
	}

	/**
	 * {@inheritDoc}
	 */
	public function withSorting(string $name, string $direction): self
	{
		$sorting = $this->getParam('sorting') ?? [];
		$sorting[$name] = $direction;

		return $this->withParam('sorting', $sorting);
	}

	/**
	 * {@inheritDoc}
	 */
	public function withDataMode(): self
	{
		return $this->withParam('mode', self::MODE_DATA);
	}

	/**
	 * {@inheritDoc}
	 */
	public function withOneMode(): self
	{
		return $this->withParam('mode', self::MODE_ONE);
	}

	/**
	 * {@inheritDoc}
	 */
	public function withCountMode(): self
	{
		return $this->withParam('mode', self::MODE_COUNT);
	}
}
