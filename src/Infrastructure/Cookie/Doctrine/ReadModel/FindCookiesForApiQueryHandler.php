<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use Generator;
use App\Domain\Cookie\Cookie;
use App\Domain\Category\Category;
use Doctrine\ORM\Query\Expr\Join;
use App\ReadModel\Cookie\CookieApiView;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\Domain\Project\ProjectHasCookieProvider;
use App\ReadModel\Cookie\FindCookiesForApiQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\BatchGeneratorFactory;

final class FindCookiesForApiQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	private BatchGeneratorFactory $batchGeneratorFactory;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                                             $em
	 * @param \SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\BatchGeneratorFactory $batchGeneratorFactory
	 */
	public function __construct(EntityManagerInterface $em, BatchGeneratorFactory $batchGeneratorFactory)
	{
		$this->em = $em;
		$this->batchGeneratorFactory = $batchGeneratorFactory;
	}

	/**
	 * @param \App\ReadModel\Cookie\FindCookiesForApiQuery $query
	 *
	 * @return \Generator
	 */
	public function __invoke(FindCookiesForApiQuery $query): Generator
	{
		$qb = $this->em->createQueryBuilder()
			->select('c.name AS cookieName, c.processingTime AS processingTime, ct.purpose AS purpose, ct.locale AS locale')
			->addSelect('cp.name AS cookieProviderName, cp.type AS cookieProviderType, cp.link AS cookieProviderLink')
			->addSelect('catt.name AS categoryName, cat.code AS categoryCode')
			->from(Cookie::class, 'c')
			->join(Category::class, 'cat', Join::WITH, 'cat.id = c.categoryId AND cat.deletedAt IS NULL AND cat.active = true')
			->join(CookieProvider::class, 'cp', Join::WITH, 'cp.id = c.cookieProviderId AND cp.deletedAt IS NULL')
			->join(ProjectHasCookieProvider::class, 'phc', Join::WITH, 'phc.cookieProviderId = cp.id AND phc.project = :projectId')
			->join('phc.project', 'p', Join::WITH, 'p.deletedAt IS NULL')
			->where('c.deletedAt IS NULL')
			->orderBy('cp.name', 'ASC')
			->addOrderBy('c.name', 'ASC')
			->setParameter('projectId', $query->projectId());

		if (NULL !== $query->locale()) {
			$qb->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
				->leftJoin('cat.translations', 'catt', Join::WITH, 'catt.locale = :locale')
				->setParameter('locale', $query->locale());
		} else {
			$qb->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = p.locales.defaultLocale')
				->leftJoin('cat.translations', 'catt', Join::WITH, 'catt.locale = p.locales.defaultLocale');
		}

		if (NULL !== $query->categoryCodes()) {
			$qb->andWhere('cat.code IN (:categoryCodes)')
				->setParameter('categoryCodes', $query->categoryCodes());
		}

		return $this->batchGeneratorFactory->create($query, $qb->getQuery(), CookieApiView::class, FALSE, FALSE);
	}
}
