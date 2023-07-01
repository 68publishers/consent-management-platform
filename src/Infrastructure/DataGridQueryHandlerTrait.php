<?php

declare(strict_types=1);

namespace App\Infrastructure;

use DateTimeZone;
use RuntimeException;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\DataGridQueryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\PaginatedResultFactory;

trait DataGridQueryHandlerTrait
{
	protected EntityManagerInterface $em;

	private PaginatedResultFactory $paginatedResultFactory;

	private int $paramsCount = 0;

	public function __construct(EntityManagerInterface $em, PaginatedResultFactory $paginatedResultFactory)
	{
		$this->em = $em;
		$this->paginatedResultFactory = $paginatedResultFactory;
	}

	/**
	 * @param string|callable $viewClassnameOrCallback
	 *
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 * @throws Exception
	 */
	protected function processQuery(DataGridQueryInterface $query, callable $countQueryBuilderFactory, callable $dataQueryBuilderFactory, $viewClassnameOrCallback, array $filterDefinitions, array $sortingDefinitions)
	{
		if ($query::MODE_COUNT === $query->mode()) {
			$qb = $countQueryBuilderFactory($query);
			assert($qb instanceof QueryBuilder || $qb instanceof DbalQueryBuilder);

			$this->applyFilters($query, $qb, $filterDefinitions);

			$this->paramsCount = 0;

			return (int) ($qb instanceof QueryBuilder ? $qb->getQuery()->getSingleScalarResult() : $qb->fetchOne());
		}

		$qb = $dataQueryBuilderFactory($query);
		assert($qb instanceof QueryBuilder || $qb instanceof DbalQueryBuilder);

		$this->applyFilters($query, $qb, $filterDefinitions);
		$this->applySorting($query, $qb, $sortingDefinitions);

		$this->paramsCount = 0;

		if ($qb instanceof DbalQueryBuilder) {
			$qb->setMaxResults($query->limit());

			if (NULL !== $query->offset()) {
				$qb->setFirstResult($query->offset());
			}

			$results = [];
			assert(is_callable($viewClassnameOrCallback));

			foreach ($qb->fetchAllAssociative() as $row) {
				$results[] = $viewClassnameOrCallback($row);
			}

			return $results;
		}

		assert(is_string($viewClassnameOrCallback));

		return $this->paginatedResultFactory->create($query, $qb->getQuery(), $viewClassnameOrCallback)->results();
	}

	/**
	 * @param QueryBuilder|DbalQueryBuilder $qb
	 */
	protected function applyFilters(DataGridQueryInterface $query, $qb, array $definitions): void
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
	 * @param QueryBuilder|DbalQueryBuilder $qb
	 */
	protected function applySorting(DataGridQueryInterface $query, $qb, array $definitions): void
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
	 * @param QueryBuilder|DbalQueryBuilder $qb
	 */
	protected function applyEquals($qb, string $column, $value): void
	{
		$p = $this->newParameterName();

		$qb->andWhere(sprintf('%s = :%s', $column, $p))
			->setParameter($p, $value);
	}

	/**
	 * @param QueryBuilder|DbalQueryBuilder $qb
	 */
	protected function applyLike($qb, string $column, string $value): void
	{
		$qb->andWhere($qb->expr()->like('LOWER(' . $column . ')', 'LOWER(' . $qb->expr()->literal("%$value%") . ')'));
	}

	/**
	 * @param QueryBuilder|DbalQueryBuilder $qb
	 *
	 * @throws \Exception
	 */
	protected function applyDate($qb, string $column, $value): void
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
	 * @param QueryBuilder|DbalQueryBuilder $qb
	 */
	protected function applyIn($qb, string $column, array $value): void
	{
		$p = $this->newParameterName();
		$type = NULL;

		if ($qb instanceof DbalQueryBuilder) {
			$firstValue = reset($value);
			$type = is_numeric($firstValue) ? Connection::PARAM_INT_ARRAY : Connection::PARAM_STR_ARRAY;
		}

		$qb->andWhere(sprintf('%s IN (:%s)', $column, $p))
			->setParameter($p, $value, $type);
	}

	/**
	 * @param QueryBuilder|DbalQueryBuilder $qb
	 */
	protected function applyJsonbContains($qb, string $column, $value): void
	{
		$condition = [];

		foreach ((array) $value as $val) {
			$p = $this->newParameterName();
			$condition[] = $qb instanceof DbalQueryBuilder
				? sprintf('%s @> :%s', $column, $p)
				: sprintf('JSONB_CONTAINS(%s, :%s) = true', $column, $p);

			$qb->setParameter($p, $val, Types::JSON);
		}

		if (0 >= count($condition)) {
			return;
		}

		if (1 === count($condition)) {
			$condition = array_shift($condition);
		} else {
			$condition = $qb instanceof DbalQueryBuilder ? $qb->expr()->or(...$condition) : $qb->expr()->orX(...$condition);
		}

		$qb->andWhere($condition);
	}

	protected function newParameterName(): string
	{
		$return = 'param_' . ($this->paramsCount + 1);
		$this->paramsCount++;

		return $return;
	}
}
