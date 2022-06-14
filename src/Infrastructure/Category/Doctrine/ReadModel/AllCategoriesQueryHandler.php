<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use App\Domain\Category\Category;
use App\ReadModel\Category\CategoryView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Category\AllCategoriesQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class AllCategoriesQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	private ViewFactoryInterface $viewFactory;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                         $em
	 * @param \SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface $viewFactory
	 */
	public function __construct(EntityManagerInterface $em, ViewFactoryInterface $viewFactory)
	{
		$this->em = $em;
		$this->viewFactory = $viewFactory;
	}

	/**
	 * @param \App\ReadModel\Category\AllCategoriesQuery $query
	 *
	 * @return \App\ReadModel\Category\CategoryView[]
	 */
	public function __invoke(AllCategoriesQuery $query): array
	{
		$data = $this->em->createQueryBuilder()
			->select('c, ct')
			->from(Category::class, 'c')
			->leftJoin('c.translations', 'ct')
			->where('c.deletedAt IS NULL')
			->orderBy('c.createdAt', 'DESC')
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(fn (array $row): CategoryView => $this->viewFactory->create(CategoryView::class, DoctrineViewData::create($row)), $data);
	}
}
