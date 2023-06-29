<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\ReadModel;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ProjectHasCookieProvider;
use App\ReadModel\CookieProvider\CookieProviderSelectOptionView;
use App\ReadModel\CookieProvider\FindCookieProviderSelectOptionsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class FindCookieProviderSelectOptionsQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\CookieProvider\FindCookieProviderSelectOptionsQuery $query
	 *
	 * @return \App\ReadModel\CookieProvider\CookieProviderSelectOptionView[]
	 */
	public function __invoke(FindCookieProviderSelectOptionsQuery $query): array
	{
		$qb = $this->em->createQueryBuilder()
			->select('c.id, c.name, c.code, c.private')
			->from(CookieProvider::class, 'c')
			->andWhere('c.deletedAt IS NULL')
			->orderBy('c.name', 'ASC');

		if (NULL !== $query->assignedProjectId()) {
			$qb->join(ProjectHasCookieProvider::class, 'phc', Join::WITH, 'phc.cookieProviderId = c.id AND phc.project = :assignedProjectId')
				->join('phc.project', 'p', Join::WITH, 'p.deletedAt IS NULL')
				->setParameter('assignedProjectId', $query->assignedProjectId());
		}

		$private = $query->private();

		if (FALSE === $private) {
			$qb->andWhere('c.private = false');
		} elseif (is_string($private) && ProjectId::isValid($private)) {
			$qb->leftJoin(Project::class, 'pr', Join::WITH, 'pr.cookieProviderId = c.id AND pr.id = :privateProjectId')
				->andWhere('(c.private = false OR pr.id IS NOT NULL)')
				->setParameter('privateProjectId', $private);
		}

		$data = $qb->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(fn (array $item): CookieProviderSelectOptionView => $this->viewFactory->create(CookieProviderSelectOptionView::class, DoctrineViewData::create($item)), $data);
	}
}
