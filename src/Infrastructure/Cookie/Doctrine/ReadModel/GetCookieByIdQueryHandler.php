<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use App\Domain\Cookie\Cookie;
use Doctrine\ORM\AbstractQuery;
use App\ReadModel\Cookie\CookieView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Cookie\GetCookieByIdQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetCookieByIdQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Cookie\GetCookieByIdQuery $query
	 *
	 * @return \App\ReadModel\Cookie\CookieView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetCookieByIdQuery $query): ?CookieView
	{
		$data = $this->em->createQueryBuilder()
			->select('c, ct')
			->from(Cookie::class, 'c')
			->leftJoin('c.translations', 'ct')
			->where('c.id = :id')
			->where('c.deletedAt IS NULL')
			->setParameter('id', $query->id())
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? $this->viewFactory->create(CookieView::class, DoctrineViewData::create($data)) : NULL;
	}
}
