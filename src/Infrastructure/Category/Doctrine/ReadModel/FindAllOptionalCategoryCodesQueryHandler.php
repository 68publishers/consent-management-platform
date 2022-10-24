<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use App\Domain\Category\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Category\FindAllOptionalCategoryCodesQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class FindAllOptionalCategoryCodesQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Category\FindAllOptionalCategoryCodesQuery $query
	 *
	 * @return string[]
	 */
	public function __invoke(FindAllOptionalCategoryCodesQuery $query): array
	{
		return $this->em->createQueryBuilder()
			->select('c.code')
			->from(Category::class, 'c')
			->where('c.deletedAt IS NULL AND c.necessary = false')
			->orderBy('c.createdAt', 'DESC')
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);
	}
}
