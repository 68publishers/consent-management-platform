<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use App\Domain\Cookie\Cookie;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use App\ReadModel\Cookie\CookieView;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\ReadModel\Cookie\GetCookieByNameAndCookieProviderQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetCookieByNameAndCookieProviderQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Cookie\GetCookieByNameAndCookieProviderQuery $query
	 *
	 * @return \App\ReadModel\Cookie\CookieView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetCookieByNameAndCookieProviderQuery $query): ?CookieView
	{
		$qb = $this->em->createQueryBuilder()
			->select('c, ct')
			->from(Cookie::class, 'c')
			->join(CookieProvider::class, 'cp', Join::WITH, 'cp.id = c.cookieProviderId AND cp.id = :cookieProviderId AND cp.deletedAt IS NULL')
			->leftJoin('c.translations', 'ct')
			->where('c.deletedAt IS NULL')
			->andWhere('c.name = :name')
			->setParameters([
				'name' => $query->name(),
				'cookieProviderId' => $query->cookieProviderId(),
			]);

		$data = $qb->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? $this->viewFactory->create(CookieView::class, DoctrineViewData::create($data)) : NULL;
	}
}
