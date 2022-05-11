<?php

declare(strict_types=1);

namespace App\Infrastructure;

use DateTimeZone;
use RuntimeException;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Orx;
use App\ReadModel\DataGridQueryInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\PaginatedResultFactory;

trait DataGridQueryHandlerTrait
{
	private int $paramsCount = 0;

	/**
	 * @param \App\ReadModel\DataGridQueryInterface $query
	 * @param callable                              $countQueryBuilderFactory
	 * @param callable                              $dataQueryBuilderFactory
	 * @param callable                              $mapper
	 * @param array                                 $filterDefinitions
	 * @param array                                 $sortingDefinitions
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	protected function processQuery(DataGridQueryInterface $query, callable $countQueryBuilderFactory, callable $dataQueryBuilderFactory, callable $mapper, array $filterDefinitions, array $sortingDefinitions)
	{
		if ($query::MODE_COUNT === $query->mode()) {
			$qb = $countQueryBuilderFactory();
			assert($qb instanceof QueryBuilder);

			$this->applyFilters($query, $qb, $filterDefinitions);

			return (int) $qb->getQuery()->getSingleScalarResult();
		}

		$qb = $dataQueryBuilderFactory();
		assert($qb instanceof QueryBuilder);

		$this->applyFilters($query, $qb, $filterDefinitions);
		$this->applySorting($query, $qb, $sortingDefinitions);

		$q = $qb->getQuery()
			->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);

		return PaginatedResultFactory::create($query, $q, $mapper)->results();
	}

	/**
	 * @param \App\ReadModel\DataGridQueryInterface $query
	 * @param \Doctrine\ORM\QueryBuilder            $qb
	 * @param array                                 $definitions
	 *
	 * @return void
	 */
	protected function applyFilters(DataGridQueryInterface $query, QueryBuilder $qb, array $definitions): void
	{
		foreach ($query->filters() as $filterName => $value) {
			if (!isset($definitions[$filterName])) {
				throw new RuntimeException(sprintf(
					'Filter "%s" is not supported by %s.',
					$filterName,
					static::class
				));
			}

			if (NULL === $value) {
				continue;
			}

			[$method, $column] = $definitions[$filterName];

			if ($query::MODE_ONE === $query->mode()) {
				$this->applyEquals($qb, $column, $value);

				continue;
			}

			$this->{$method}($qb, $column, $value);
		}
	}

	/**
	 * @param \App\ReadModel\DataGridQueryInterface $query
	 * @param \Doctrine\ORM\QueryBuilder            $qb
	 * @param array                                 $definitions
	 *
	 * @return void
	 */
	protected function applySorting(DataGridQueryInterface $query, QueryBuilder $qb, array $definitions): void
	{
		foreach ($query->sorting() as $name => $direction) {
			if (!isset($definitions[$name])) {
				throw new RuntimeException(sprintf(
					'Sorting "%s" is not supported by %s.',
					$name,
					static::class
				));
			}

			foreach ((array) $definitions[$name] as $column) {
				$qb->addOrderBy($column, $direction);
			}
		}
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $qb
	 * @param string                     $column
	 * @param mixed                      $value
	 *
	 * @return void
	 */
	protected function applyEquals(QueryBuilder $qb, string $column, $value): void
	{
		$p = $this->newParameterName();

		$qb->andWhere(sprintf('%s = :%s', $column, $p))
			->setParameter($p, $value);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $qb
	 * @param string                     $column
	 * @param string                     $value
	 *
	 * @return void
	 */
	protected function applyLike(QueryBuilder $qb, string $column, string $value): void
	{
		$qb->andWhere($qb->expr()->like('LOWER(' . $column . ')', 'LOWER(' . $qb->expr()->literal("%$value%") . ')'));
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $qb
	 * @param string                     $column
	 * @param mixed                      $value
	 *
	 * @return void
	 * @throws \Exception
	 */
	protected function applyDate(QueryBuilder $qb, string $column, $value): void
	{
		if (!$value instanceof DateTimeInterface) {
			$value = new DateTimeImmutable($value, new DateTimeZone('UTC'));
		}

		$p1 = $this->newParameterName();
		$p2 = $this->newParameterName();

		$qb->andWhere(sprintf('%s >= :%s AND %s <= :%s', $column, $p1, $column, $p2))
			->setParameter($p1, $value->format('Y-m-d 00:00:00'))
			->setParameter($p2, $value->format('Y-m-d 23:59:59'));
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $qb
	 * @param string                     $column
	 * @param array                      $value
	 *
	 * @return void
	 */
	protected function applyIn(QueryBuilder $qb, string $column, array $value): void
	{
		$p = $this->newParameterName();

		$qb->andWhere(sprintf('%s IN (:%s)', $column, $p))
			->setParameter($p, $value);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $qb
	 * @param string                     $column
	 * @param $value
	 *
	 * @return void
	 * @throws \JsonException
	 */
	protected function applyJsonbContains(QueryBuilder $qb, string $column, $value): void
	{
		$condition = [];

		foreach ((array) $value as $val) {
			$p = $this->newParameterName();
			$condition[] = sprintf('JSONB_CONTAINS(%s, :%s) = true', $column, $p);

			$qb->setParameter($p, json_encode($val, JSON_THROW_ON_ERROR));
		}

		if (0 >= count($condition)) {
			return;
		}

		if (1 === count($condition)) {
			$condition = array_shift($condition);
		} else {
			$condition = new Orx($condition);
		}

		$qb->andWhere($condition);
	}

	/**
	 * @return string
	 */
	protected function newParameterName(): string
	{
		$return = 'param_' . ($this->paramsCount + 1);
		$this->paramsCount++;

		return $return;
	}
}
