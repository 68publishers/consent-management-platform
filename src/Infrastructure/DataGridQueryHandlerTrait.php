<?php

declare(strict_types=1);

namespace App\Infrastructure;

use DateTimeZone;
use RuntimeException;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\DataGridQueryInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\PaginatedResultFactory;

trait DataGridQueryHandlerTrait
{
	protected EntityManagerInterface $em;

	private PaginatedResultFactory $paginatedResultFactory;

	private int $paramsCount = 0;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                                              $em
	 * @param \SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\PaginatedResultFactory $paginatedResultFactory
	 */
	public function __construct(EntityManagerInterface $em, PaginatedResultFactory $paginatedResultFactory)
	{
		$this->em = $em;
		$this->paginatedResultFactory = $paginatedResultFactory;
	}

	/**
	 * @param \App\ReadModel\DataGridQueryInterface $query
	 * @param callable                              $countQueryBuilderFactory
	 * @param callable                              $dataQueryBuilderFactory
	 * @param string                                $viewClassname
	 * @param array                                 $filterDefinitions
	 * @param array                                 $sortingDefinitions
	 *
	 * @return int|array
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	protected function processQuery(DataGridQueryInterface $query, callable $countQueryBuilderFactory, callable $dataQueryBuilderFactory, string $viewClassname, array $filterDefinitions, array $sortingDefinitions)
	{
		if ($query::MODE_COUNT === $query->mode()) {
			$qb = $countQueryBuilderFactory($query);
			assert($qb instanceof QueryBuilder);

			$this->applyFilters($query, $qb, $filterDefinitions);

			$this->paramsCount = 0;

			return (int) $qb->getQuery()->getSingleScalarResult();
		}

		$qb = $dataQueryBuilderFactory($query);
		assert($qb instanceof QueryBuilder);

		$this->applyFilters($query, $qb, $filterDefinitions);
		$this->applySorting($query, $qb, $sortingDefinitions);

		$this->paramsCount = 0;

		return $this->paginatedResultFactory->create($query, $qb->getQuery(), $viewClassname)->results();
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

			$def = $definitions[$filterName];

			if (!isset($def[2])) {
				$def[2] = [];
			}

			[$method, $column, $extraArgs] = $def;
			$extraArgs = is_array($extraArgs) ? $extraArgs : [$extraArgs];

			if ($query::MODE_ONE === $query->mode()) {
				$this->applyEquals($qb, $column, $value);

				continue;
			}

			$this->{$method}($qb, $column, $value, ...$extraArgs);
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

		$from = (clone $value)->setTime(0, 0)->setTimezone(new DateTimeZone('UTC'));
		$to = (clone $value)->setTime(23, 59, 59)->setTimezone(new DateTimeZone('UTC'));

		$p1 = $this->newParameterName();
		$p2 = $this->newParameterName();

		$qb->andWhere(sprintf('%s >= :%s AND %s <= :%s', $column, $p1, $column, $p2))
			->setParameter($p1, $from->format('Y-m-d H:i:s'))
			->setParameter($p2, $to->format('Y-m-d H:i:s'));
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
