<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\QueryHandler;

use Doctrine\ORM\AbstractQuery;
use App\Domain\Category\Category;
use App\ReadModel\Category\CategoryView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Category\GetCategoryByIdQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class GetCategoryByIdQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param \App\ReadModel\Category\GetCategoryByIdQuery $query
	 *
	 * @return \App\ReadModel\Category\CategoryView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetCategoryByIdQuery $query): ?CategoryView
	{
		$data = $this->em->createQueryBuilder()
			->select('c, ct')
			->from(Category::class, 'c')
			->leftJoin('c.translations', 'ct')
			->where('c.id = :id')
			->setParameter('id', $query->id())
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? ViewFactory::createCategoryView($data) : NULL;
	}
}
