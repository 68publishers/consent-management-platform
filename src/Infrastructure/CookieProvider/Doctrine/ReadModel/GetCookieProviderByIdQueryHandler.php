<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\ReadModel\CookieProvider\GetCookieProviderByIdQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetCookieProviderByIdQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\CookieProvider\GetCookieProviderByIdQuery $query
	 *
	 * @return \App\ReadModel\CookieProvider\CookieProviderView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetCookieProviderByIdQuery $query): ?CookieProviderView
	{
		$data = $this->em->createQueryBuilder()
			->select('c, ct')
			->from(CookieProvider::class, 'c')
			->leftJoin('c.translations', 'ct')
			->where('c.id = :id')
			->setParameter('id', $query->id())
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? $this->viewFactory->create(CookieProviderView::class, DoctrineViewData::create($data)) : NULL;
	}
}
