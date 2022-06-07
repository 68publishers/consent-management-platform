<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\QueryHandler;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\ReadModel\CookieProvider\GetCookieProviderByCodeQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class GetCookieProviderByCodeQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\CookieProvider\GetCookieProviderByCodeQuery $query
	 *
	 * @return \App\ReadModel\CookieProvider\CookieProviderView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetCookieProviderByCodeQuery $query): ?CookieProviderView
	{
		$data = $this->em->createQueryBuilder()
			->select('c, ct')
			->from(CookieProvider::class, 'c')
			->leftJoin('c.translations', 'ct')
			->where('LOWER(c.code) = LOWER(:code)')
			->andWhere('c.deletedAt IS NULL')
			->setParameter('code', $query->code())
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? ViewFactory::createCookieProviderView($data) : NULL;
	}
}
