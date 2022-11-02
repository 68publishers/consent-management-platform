<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Cookie\Cookie;
use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\ReadModel\Project\ProjectCookieTotalsView;
use App\ReadModel\Project\CalculateProjectCookieTotalsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class CalculateProjectCookieTotalsQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Project\CalculateProjectCookieTotalsQuery $query
	 *
	 * @return \App\ReadModel\Project\ProjectCookieTotalsView
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(CalculateProjectCookieTotalsQuery $query): ProjectCookieTotalsView
	{
		$data = $this->em->createQueryBuilder()
			->select('COUNT(DISTINCT cp.id) AS providers, COUNT(DISTINCT c.id) AS commonCookies, COUNT(DISTINCT c_private.id) AS privateCookies')
			->from(Project::class, 'p')
			->leftJoin('p.cookieProviders', 'phcp')
			->leftJoin(CookieProvider::class, 'cp', Join::WITH, 'cp.id = phcp.cookieProviderId AND cp.createdAt <= :maxDate AND (cp.deletedAt IS NULL OR cp.deletedAt > :maxDate)')
			->leftJoin(Cookie::class, 'c', Join::WITH, 'c.cookieProviderId = cp.id AND c.createdAt <= :maxDate AND (c.deletedAt IS NULL OR c.deletedAt > :maxDate)')
			->leftJoin(CookieProvider::class, 'cp_private', Join::WITH, 'cp_private.id = p.cookieProviderId AND cp_private.createdAt <= :maxDate AND (cp_private.deletedAt IS NULL OR cp_private.deletedAt > :maxDate)')
			->leftJoin(Cookie::class, 'c_private', Join::WITH, 'c_private.cookieProviderId = cp_private.id AND c_private.createdAt <= :maxDate AND (c_private.deletedAt IS NULL OR c_private.deletedAt > :maxDate)')
			->where('p.id = :projectId')
			->setParameters([
				'projectId' => $query->projectId(),
				'maxDate' => $query->maxDate(),
			])
			->getQuery()
			->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

		return $this->viewFactory->create(ProjectCookieTotalsView::class, DoctrineViewData::create($data));
	}
}
