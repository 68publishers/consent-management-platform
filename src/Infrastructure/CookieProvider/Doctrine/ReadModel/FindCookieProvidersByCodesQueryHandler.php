<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\ReadModel\CookieProvider\FindCookieProvidersByCodesQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class FindCookieProvidersByCodesQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\CookieProvider\FindCookieProvidersByCodesQuery $query
	 *
	 * @return \App\ReadModel\CookieProvider\CookieProviderView[]
	 */
	public function __invoke(FindCookieProvidersByCodesQuery $query): array
	{
		$data = $this->em->createQueryBuilder()
			->select('c, ct')
			->from(CookieProvider::class, 'c')
			->leftJoin('c.translations', 'ct')
			->where('LOWER(c.code) IN (:codes)')
			->andWhere('c.deletedAt IS NULL')
			->setParameter('codes', array_map('strtolower', $query->codes()))
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(fn (array $item): CookieProviderView => $this->viewFactory->create(CookieProviderView::class, DoctrineViewData::create($item)), $data);
	}
}
