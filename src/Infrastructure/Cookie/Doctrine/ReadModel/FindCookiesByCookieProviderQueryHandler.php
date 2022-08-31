<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use App\Domain\Cookie\Cookie;
use Doctrine\ORM\AbstractQuery;
use App\ReadModel\Cookie\CookieView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Cookie\FindCookiesByCookieProviderQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class FindCookiesByCookieProviderQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Cookie\FindCookiesByCookieProviderQuery $query
	 *
	 * @return iterable|\App\ReadModel\Cookie\CookieView[]
	 */
	public function __invoke(FindCookiesByCookieProviderQuery $query): iterable
	{
		$data = $this->em->createQueryBuilder()
			->select('c, ct')
			->from(Cookie::class, 'c')
			->leftJoin('c.translations', 'ct')
			->where('c.cookieProviderId = :cookieProviderId')
			->andWhere('c.deletedAt IS NULL')
			->setParameter('cookieProviderId', $query->cookieProviderId())
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(fn (array $item): CookieView => $this->viewFactory->create(CookieView::class, DoctrineViewData::create($item)), $data);
	}
}
