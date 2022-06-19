<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\ReadModel;

use Generator;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\Domain\Project\ProjectHasCookieProvider;
use App\ReadModel\CookieProvider\CookieProviderApiView;
use App\ReadModel\CookieProvider\FindCookieProvidersForApiQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\BatchGeneratorFactory;

final class FindCookieProvidersForApiQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\CookieProvider\FindCookieProvidersForApiQuery $query
	 *
	 * @return \Generator
	 */
	public function __invoke(FindCookieProvidersForApiQuery $query): Generator
	{
		$qb = $this->em->createQueryBuilder()
			->select('c.id, c.name, c.type, c.link, ct.purpose')
			->from(CookieProvider::class, 'c')
			->join(ProjectHasCookieProvider::class, 'phc', Join::WITH, 'phc.cookieProviderId = c.id AND phc.project = :projectId')
			->join('phc.project', 'p', Join::WITH, 'p.deletedAt IS NULL')
			->where('c.deletedAt IS NULL')
			->orderBy('c.name', 'ASC')
			->setParameter('projectId', $query->projectId());

		if (NULL !== $query->locale()) {
			$qb->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
				->setParameter('locale', $query->locale());
		} else {
			$qb->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = p.locales.defaultLocale');
		}

		return $this->batchGeneratorFactory->create($query, $qb->getQuery(), CookieProviderApiView::class, FALSE, FALSE);
	}
}
